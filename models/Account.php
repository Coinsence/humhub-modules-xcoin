<?php

namespace humhub\modules\xcoin\models;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

/**
 * This is the model class for table "xcoin_account".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $space_id
 * @property integer $account_type 
 * @property string $title
 *
 * @property Space $space
 * @property User $user
 * @property TcoinTransaction[] $xcoinTransactions
 * @property TcoinTransaction[] $xcoinTransactions0
 */
class Account extends \yii\db\ActiveRecord
{

    const TYPE_STANDARD = 1;
    const TYPE_ISSUE = 2;
    const TYPE_FUNDING = 3;
    const TYPE_DEFAULT = 4;

    public $editFieldManager;

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
            #[['user_id', 'space_id'], 'integer'],
            [['title'], 'string', 'max' => 100],
            [['editFieldManager'], 'safe'],
                #[['user_id', 'space_id', 'title'], 'unique', 'targetAttribute' => ['user_id', 'space_id', 'title'], 'message' => 'The combination of User ID, Space ID and Title has already been taken.'],
                #[['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::className(), 'targetAttribute' => ['space_id' => 'id']],
                #[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
        /*
          return [
          [['user_id', 'title'], 'required'],
          [['user_id', 'space_id'], 'integer'],
          [['title'], 'string', 'max' => 100],
          [['editFieldManager'], 'safe'],
          [['user_id', 'space_id', 'title'], 'unique', 'targetAttribute' => ['user_id', 'space_id', 'title'], 'message' => 'The combination of User ID, Space ID and Title has already been taken.'],
          [['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::className(), 'targetAttribute' => ['space_id' => 'id']],
          [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
          ];
         *
         */
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
            $this->user_id = new \yii\db\Expression('NULL');
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpace()
    {
        return $this->hasOne(Space::className(), ['id' => 'space_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionsFrom()
    {
        return $this->hasMany(Transaction::className(), ['from_account_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactionsTo()
    {
        return $this->hasMany(Transaction::className(), ['to_account_id' => 'id']);
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
     * @return AccountBalance[] the account balances by asset
     */
    public function getBalances()
    {
        return $this->hasMany(AccountBalance::className(), ['account_id' => 'id']);
    }

    /**
     * Calculate account asset balance
     *
     * @param \humhub\modules\xcoin\models\Asset $asset
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

}
