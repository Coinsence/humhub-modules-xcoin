<?php
/**
 * Created By IJ
 * @author : Ala Daly <ala.daly@dotit-corp.com>
 * @date : 7‏/9‏/2022, Wed
 **/

namespace humhub\modules\xcoin\models;

use yii\db\ActiveRecord;


/**
 * This is the model class for table "xcoin_account_voucher".
 *
 * @property integer $id
 * @property string $value
 * @property string $tag
 * @property integer $status
 * @property integer $account_id
 * @property integer $redeemed_account_id
 * @property integer $asset_id
 * @property float $amount
 *
 * @property Asset $asset
 * @property Account $account
 * @property Account $redeemed_account
 */
class AccountVoucher extends ActiveRecord
{
    // Voucher status
    const STATUS_USED = 0;
    const STATUS_READY = 1;
    const STATUS_DISABLED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_account_voucher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['asset_id', 'value', 'account_id', 'amount','tag'], 'required'],
            [['asset_id', 'account_id', 'redeemed_account_id'], 'integer'],
            [['amount'], 'number', 'min' => 0.001],
            [['created_at'], 'safe'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['account_id' => 'id']],
            [['redeemed_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['redeemed_account_id' => 'id']],
        ];
    }

    public function getAsset()
    {
        return $this->hasOne(Asset::className(), ['id' => 'asset_id']);
    }

    public function getAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'account_id']);

    }

    public function getRedeemedAccount()
    {
        return $this->hasOne(Account::className(), ['id' => 'redeemed_account_id']);
    }
    public function isVoucherReady(){
        return $this->status === AccountVoucher::STATUS_READY;
    }
}
