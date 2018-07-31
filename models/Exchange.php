<?php

namespace humhub\modules\xcoin\models;

use Yii;

/**
 * This is the model class for table "xcoin_exchange".
 *
 * @property integer $id
 * @property integer $account_id
 * @property integer $asset_id
 * @property integer $available_amount
 * @property integer $minimum_amount
 * @property integer $wanted_asset_id
 * @property integer $exchange_rate
 * @property string $created_at
 * @property integer $created_by
 *
 * @property Account $account
 * @property Asset $asset
 * @property Asset $wantedAsset
 */
class Exchange extends \yii\db\ActiveRecord
{

    const SCENARIO_OFFER = 'offer';


    public $amountx;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_exchange';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['asset_id'], 'required'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::class, 'targetAttribute' => ['asset_id' => 'id']],

            [['available_amount'], 'required'],
            [['available_amount'], 'number', 'min' => '0.001'],

            [['exchange_rate'], 'required'],
            [['exchange_rate'], 'number', 'min' => '0.001'],

            [['wanted_asset_id'], 'required'],
            [['wanted_asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::class, 'targetAttribute' => ['wanted_asset_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_OFFER => ['asset_id', 'available_amount', 'exchange_rate', 'wanted_asset_id'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'account_id' => 'Account ID',
            'asset_id' => 'Asset',
            'available_amount' => 'Amount',
            'minimum_amount' => 'Min.',
            'wanted_asset_id' => 'Requested Asset',
            'created_at' => 'Created At',
        ];
    }

    public function attributeHints()
    {
        return [
            'available_amount' => 'The amount of assets to be exchanged.',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);
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
    public function getWantedAsset()
    {
        return $this->hasOne(Asset::className(), ['id' => 'wanted_asset_id']);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if (empty($this->created_by)) {
                $this->created_by = Yii::$app->user->id;
            }
        }
        return parent::beforeSave($insert);
    }


    /**
     * Returns the number of
     *
     * @return int
     */
    public function getAvailableAmountValidated()
    {
        $availableAmountAccount = $this->account->getAssetBalance($this->asset);

        if ($availableAmountAccount < $this->available_amount) {
            return $availableAmountAccount;
        }

        return $this->available_amount;
    }

}
