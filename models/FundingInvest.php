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
            'amountBuy' => Yii::t('XcoinModule.base', 'Quantity'),
            'amountPay' => Yii::t('XcoinModule.base', 'Invest'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        $max = $this->getMaxBuyAmount();

        return [
            'amountBuy' => Yii::t('XcoinModule.base', 'Received amount. (Maximum: {0})', [$max]),
            'amountPay' => Yii::t('XcoinModule.base', 'Rate: 1 to {0}', [$this->funding->exchange_rate]),
        ];
    }

    /**
     * @return int
     */
    protected function getMaxBuyAmount()
    {
        $left = $this->funding->getAvailableAmount();

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
        $transaction->comment = Yii::t('XcoinModule.base', 'Funding Invest');
        if (!$transaction->save()) {
            Yii::error(Yii::t('XcoinModule.base','Buy transaction failed: {0} amount: {1} asset Id: {2} from acc: {3}', [
                print_r($transaction->getErrors(), 1),
                $this->getBuyAmount(),
                $this->getBuyAsset()->id,
                $this->fromAccount->id
            ]), 'xcoin.base');
            throw new HttpException(Yii::t('XcoinModule.base', 'Transaction failed!'));
        }

        // Pay Transaction
        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
        $transaction->asset_id = $this->getPayAsset()->id;
        $transaction->to_account_id = $fundingAccount->id;
        $transaction->from_account_id = $this->fromAccount->id;
        $transaction->amount = $this->amountPay;
        $transaction->comment = Yii::t('XcoinModule.base', 'Funding Invest');
        if (!$transaction->save()) {
            Yii::error(Yii::t('XcoinModule.base','Pay transaction failed: {0} amount: {1} asset Id: {2} from acc: {3}', [
                print_r($transaction->getErrors(), 1),
                $this->amountpay,
                $this->getPayAsset()->id,
                $fundingAccount->id
            ]), 'xcoin.base');
            throw new HttpException(Yii::t('XcoinModule.base', 'Transaction failed!'));
        }

        return true;
    }

}
