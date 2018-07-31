<?php

namespace humhub\modules\xcoin\models;

use humhub\modules\xcoin\helpers\AssetHelper;
use Yii;
use yii\base\Model;
use yii\web\HttpException;


/**
 * Class FundingInvest
 * @package humhub\modules\xcoin\models
 *
 */
class FundingInvest extends Model
{

    /**
     * @var Account
     */
    public $fromAccount;

    /**
     * @var Funding
     */
    public $funding;

    /**
     * @var int
     */
    public $amountBuy;

    /**
     * @var int
     */
    public $amountPay;


    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        #$this->addError('amount', 'Foobar');
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['amountPay'], 'required'],
            ['amountPay', 'number', 'min' => 0.001, 'max' => $this->getMaxBuyAmount()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'amountBuy' => 'Quantity',
            'amountPay' => 'Invest',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        $max = $this->getMaxBuyAmount();

        return [
            'amountBuy' => 'Received amount. (Maximum: ' . $max . ')',
            'amountPay' => Yii::t('XcoinModule.base', 'Rate: 1 to ') . $this->funding->exchange_rate,
        ];
    }

    /**
     * @return int
     */
    protected function getMaxBuyAmount()
    {
        $left = $this->funding->getBaseMaximumAmount();

        // Check max amount of current account
        $accountLeft = $this->fromAccount->getAssetBalance($this->funding->asset);

        if ($accountLeft < $left) {
            return $accountLeft;
        }

        return $left;
    }


    public function getPayAsset()
    {
        return $this->funding->asset;
    }

    /**
     * @return Asset
     */
    public function getBuyAsset()
    {
        return AssetHelper::getSpaceAsset($this->funding->space);
    }


    public function getBuyAmount() {
        return $this->amountPay * $this->funding->exchange_rate;
    }


    public function invest()
    {
        if (!$this->validate()) {
            return false;
        }

        $fundingAccount = $this->funding->getFundingAccount();

        // Buy Transaction
        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
        $transaction->asset_id = $this->getBuyAsset()->id;
        $transaction->to_account_id = $this->fromAccount->id;
        $transaction->from_account_id = $fundingAccount->id;
        $transaction->amount = $this->getBuyAmount();
        $transaction->comment = 'Funding Invest';
        if (!$transaction->save()) {
            Yii::error('Buy transaction failed: ' . print_r($transaction->getErrors(), 1). ' amount: '. $this->getBuyAmount(). ' asset Id'. $this->getBuyAsset()->id. ' from acc: '. $this->fromAccount->id, 'xcoin.funding');
            throw new HttpException('Transaction failed!');
        }

        // Pay Transaction
        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
        $transaction->asset_id = $this->getPayAsset()->id;
        $transaction->to_account_id = $fundingAccount->id;
        $transaction->from_account_id = $this->fromAccount->id;
        $transaction->amount = $this->amountPay;
        $transaction->comment = 'Funding Invest';
        if (!$transaction->save()) {
            Yii::error('Pay transaction failed: ' . print_r($transaction->getErrors(), 1). ' amount: '. $this->amountpay. ' asset Id'. $this->getPayAsset()->id. ' from acc: '. $fundingAccount->id, 'xcoin.funding');
            throw new HttpException('Transaction failed!');
        }

        // Update
        $newAvail = $this->funding->available_amount - $this->getBuyAmount();
        $this->funding->updateAttributes(['available_amount' => $newAvail]);

        return true;
    }

}
