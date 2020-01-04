<?php

namespace humhub\modules\xcoin\models;

use Yii;
use humhub\components\behaviors\GUID;
use humhub\components\Event;
use humhub\modules\tasks\models\account\TaskAccount;
use humhub\modules\tasks\models\Task;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "xcoin_account".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $space_id
 * @property integer $account_type
 * @property string $title
 * @property string $guid
 * @property string $ethereum_address
 * @property integer $funding_id
 * @property integer $investor_id
 *
 * @property Space $space
 * @property User $user
 * @preperty TaskAccount $account
 * @property Task $task
 * @property Task $funding
 * @property User $investor
 */
class Account extends ActiveRecord
{

    const TYPE_STANDARD = 1;
    const TYPE_ISSUE = 2;
    const TYPE_FUNDING = 3;
    const TYPE_DEFAULT = 4;
    const TYPE_TASK = 5;
    const TYPE_COMMUNITY_INVESTOR = 6;

    /** @var Event this event is dispatched when account with
     * TYPE_DEFAULT is created for space in order to create ethereum DAO
     */
    const EVENT_DEFAULT_SPACE_ACCOUNT_CREATED = 'defaultSpaceAccountCreated';

    public $editFieldManager;

    public $mnemonic;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_account';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title'], 'string', 'max' => 100],
            [['editFieldManager'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            GUID::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('XcoinModule.base', 'ID'),
            'user_id' => Yii::t('XcoinModule.base', 'User ID'),
            'space_id' => Yii::t('XcoinModule.base', 'Space ID'),
            'title' => Yii::t('XcoinModule.base', 'Title'),
            'editFieldManager' => Yii::t('XcoinModule.base', 'Manager')
        ];
    }

    public function afterFind()
    {
        if ($this->user !== null) {
            $this->editFieldManager[] = $this->user->guid;
        }
    }

    public function beforeSave($insert)
    {
        if ($insert && isset($this->editFieldManager[0])) {
            $manager = User::findOne(['guid' => $this->editFieldManager[0]]);
            if ($manager !== null) {
                $this->user_id = $manager->id;
            }
        }

        if (empty($this->user_id)) {
            $this->user_id = new Expression('NULL');
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpace()
    {
        return $this->hasOne(Space::class, ['id' => 'space_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionsFrom()
    {
        return $this->hasMany(Transaction::class, ['from_account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionsTo()
    {
        return $this->hasMany(Transaction::class, ['to_account_id' => 'id']);
    }

    /**
     * Returns all assets used in this account
     *
     * @return Asset[] the assets
     */
    public function getAssets()
    {
        $assets = [];

        $balances = AccountBalance::find()->where(['account_id' => $this->id])->andWhere('balance != 0')->all();
        foreach ($balances as $balance) {
            $assets[] = $balance->asset;
        }

        return $assets;
    }

    /**
     * Returns all balances used in this account by asset
     *
     * @return ActiveQuery
     */
    public function getBalances()
    {
        return $this->hasMany(AccountBalance::class, ['account_id' => 'id']);
    }

    /**
     * Calculate account asset balance
     *
     * @param Asset $asset
     * @return int the current balance
     */
    public function getAssetBalance(Asset $asset)
    {
        $plus = Transaction::find()->where(['to_account_id' => $this->id])->andWhere(['asset_id' => $asset->id])->sum('amount');
        $minus = Transaction::find()->where(['from_account_id' => $this->id])->andWhere(['asset_id' => $asset->id])->sum('amount');

        return round($plus, 4) - round($minus, 4);
    }

    public function isEmpty()
    {
        foreach ($this->getAssets() as $asset) {
            if (!empty($this->getAssetBalance($asset))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return ActiveQuery
     */
    public function getTaskAccount()
    {
        return $this->hasOne(TaskAccount::class, ['account_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getTask()
    {
        return $this->hasOne(Task::class, ['id' => 'task_id'])->via('taskAccount');
    }

    /**
     * @return ActiveQuery
     */
    public function getFunding()
    {
        return $this->hasOne(Funding::class, ['id' => 'funding_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getInvestor()
    {
        return $this->hasOne(User::class, ['id' => 'investor_id']);
    }

    public function revertTransactions()
    {
        // revert all transactions
        foreach (
            Transaction::find()
                ->where(['to_account_id' => $this->id])
                ->orWhere(['from_account_id' => $this->id])->all()
            as $transaction
        ) {


            $revertTransaction = new Transaction();

            $revertTransaction->asset_id = $transaction->asset_id;
            $revertTransaction->transaction_type = $transaction->transaction_type;
            $revertTransaction->amount = $transaction->amount;
            $revertTransaction->comment = $transaction->comment ? "$transaction->comment - Transaction Reverted" : "Campaign issue transaction reverted";
            $revertTransaction->from_account_id = $transaction->to_account_id;

            if($transaction->transaction_type == Transaction::TRANSACTION_TYPE_ISSUE){
                // send coin to default account rather than issue account
                $defaultAccount = Account::findOne([
                    'space_id' => $this->getSpace()->one()->id,
                    'account_type' => Account::TYPE_DEFAULT
                ]);

                $revertTransaction->to_account_id = $defaultAccount->id;

            } else {
                $revertTransaction->to_account_id = $transaction->from_account_id;
            }

            $revertTransaction->save();
        }
    }

    public function disable()
    {
        $this->revertTransactions();
        // TODO disable account
    }
}
