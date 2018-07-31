<?php

namespace humhub\modules\xcoin\models;

use Yii;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\helpers\AssetHelper;

/**
 * This is the model class for table "xcoin_funding".
 *
 * @property integer $id
 * @property integer $space_id
 * @property integer $asset_id
 * @property integer $exchange_rate
 * @property integer $total_amount
 * @property integer $available_amount
 * @property string $created_at
 * @property integer $created_by
 *
 * @property TcoinAsset $asset
 * @property User $createdBy
 * @property User $space
 */
class Funding extends \yii\db\ActiveRecord
{

    const SCENARIO_EDIT = 'sedit';

    public $amount = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_funding';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //  'total_amount', 'available_amount',
            [['space_id', 'asset_id', 'exchange_rate', 'created_by', 'available_amount'], 'required'],
            [['space_id', 'asset_id', 'total_amount', 'created_by'], 'integer'],
            [['available_amount'], 'number', 'min' => '0'],
            [['exchange_rate'], 'number', 'min' => '0.001'],
            [['created_at'], 'safe'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::class, 'targetAttribute' => ['asset_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::class, 'targetAttribute' => ['space_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_EDIT => ['asset_id', 'exchange_rate', 'available_amount'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'space_id' => 'Space ID',
            'asset_id' => 'Requested asset',
            'exchange_rate' => 'Exchange rate',
            'total_amount' => 'Total Amount',
            'available_amount' => 'Amount',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'amount' => 'Offered asset',
            'amountConverted' => 'Wanted',
        ];
    }

    public function attributeHints()
    {
        return [
            'available_amount' => 'Maximum amount that can be exchanged with the asset at the specified rate. The available amount will also be automatically adjusted if the funds in the funding account are not sufficient. If set to 0 this exchange option is disabled.'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(Asset::className(), ['id' => 'asset_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpace()
    {
        return $this->hasOne(Space::className(), ['id' => 'space_id']);
    }

    
    /**
     * Gets the amount in the target asset
     * 
     * @return type
     */
    /*
    public function getMaximumAmount()
    {
        print $this->getBaseMaximumAmount();
        return $this->getBaseMaximumAmount() / $this->exchange_rate;
    }
     * *
     */
    
    /**
     * Returns the available funding space assets based on funding account balance and available amount
     * 
     * @return int
     */
    public function getBaseMaximumAmount()
    {
        // Funding Account Balance
        $balance = AccountHelper::getFundingAccountBalance($this->space);

        if ($balance < $this->available_amount) {
            return $balance;
        }

        return $this->available_amount;
    }

    public function getFundingAccount()
    {
        return AccountHelper::getFundingAccount($this->space);
    }
    

}
