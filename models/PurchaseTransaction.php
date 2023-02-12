<?php

namespace humhub\modules\xcoin\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "xcoin_purchase_coin_transaction".
 *
 * @property integer $id
 * @property integer $asset_id
 * @property integer $transaction_type
 * @property integer $to_account_id
 * @property integer $from_account_id
 * @property integer $amount
 * @property string $key
 * @property string $created_at
 *
 * @property Asset $asset
 * @property Account $fromAccount
 * @property Account $toAccount
 */
class PurchaseTransaction extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_purchase_coin_transaction';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['asset_id', 'to_account_id', 'amount'], 'required'],
            [['asset_id', 'transaction_type', 'to_account_id', 'from_account_id'], 'integer'],
            [['amount'], 'number', 'min' => 0.001],
            [['created_at'], 'safe'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['from_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['from_account_id' => 'id']],
            [['to_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['to_account_id' => 'id']],
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert)
                $this->created_at = date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(Asset::className(), ['id' => 'asset_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFromAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'from_account_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getToAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'to_account_id']);
    }
}
