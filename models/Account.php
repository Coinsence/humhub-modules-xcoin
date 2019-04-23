<?php

namespace humhub\modules\xcoin\models;

use humhub\components\behaviors\GUID;
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
 *
 * @property Space $space
 * @property User $user
 * @preperty TaskAccount $account
 * @property Task $task
 */
class Account extends ActiveRecord
{

    const TYPE_STANDARD = 1;
    const TYPE_ISSUE = 2;
    const TYPE_FUNDING = 3;
    const TYPE_DEFAULT = 4;
    const TYPE_TASK = 5;

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
            'id' => 'ID',
            'user_id' => 'User ID',
            'space_id' => 'Space ID',
            'title' => 'Title',
            'editFieldManager' => 'Manager'
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

}
