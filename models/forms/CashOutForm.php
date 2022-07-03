<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2021 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\models\forms;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Transaction;
use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;

/**
 * This is the model form class for CashOut form.
 */
class CashOutForm extends Model
{
    /** @var int */
    public $amount;

    /** @var string */
    public $accountOwner;

    /** @var string */
    public $bankName;

    /** @var string */
    public $iban;

    /** @var string */
    public $bic;

    /** @var string */
    public $swift;

    /** @var string */
    public $country;

    /** @var string */
    public $address;

    /** @var string */
    public $state;

    /** @var string */
    public $city;

    /** @var string */
    public $zipCode;

    /** @var Account */
    public $senderAccount;

    /** @var Asset */
    private $cashOutAsset;

    /** @var Space */
    private $cashOutSpace;

    /** @var Account */
    private $cashOutAccount;

    /** @var Transaction */
    private $cashoutTransaction;

    /** @var string */
    private $cashOutAssetName;

    /** @var string */
    private $cashOutBridge;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->cashOutSpace = Space::findOne(['name' => Yii::$app->params['coinCashOut']['space']]);
        $this->cashOutAsset = Asset::findOne(['space_id' => $this->cashOutSpace->id]);
        $this->cashOutAccount = Account::findOne(['algorand_address' => Yii::$app->params['coinCashOut']['redeem-account-eth-address']]);
        $this->cashOutAssetName = $this->cashOutSpace->name;
        $this->cashOutBridge =  Yii::$app->params['coinCashOut']['bridge'];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'amount',
                    'accountOwner',
                    'bankName', 'iban',
                    'bic',
                    'swift',
                    'country',
                    'address',
                    'state',
                    'city',
                    'zipCode'
                ]
                , 'required'
            ],
            [
                [
                    'accountOwner',
                    'bankName',
                    'iban',
                    'bic',
                    'swift',
                    'country',
                    'address',
                    'state',
                    'city',
                    'zipCode'
                ]
                , 'safe'
            ],
            ['amount', 'integer', 'min' => 10, 'max' => $this->getCoinBalance()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'amount' => Yii::t('XcoinModule.forms_CashOutForm', 'Number of COINs'),
            'accountOwner' => Yii::t('XcoinModule.forms_CashOutForm', 'Account Owner'),
            'bankName' => Yii::t('XcoinModule.forms_CashOutForm', 'Bank Name'),
            'iban' => Yii::t('XcoinModule.forms_CashOutForm', 'IBAN'),
            'bic' => Yii::t('XcoinModule.forms_CashOutForm', 'BIC'),
            'swift' => Yii::t('XcoinModule.forms_CashOutForm', 'SWIFT'),
            'country' => Yii::t('XcoinModule.forms_CashOutForm', 'Country'),
            'address' => Yii::t('XcoinModule.forms_CashOutForm', 'Address'),
            'state' => Yii::t('XcoinModule.forms_CashOutForm', 'State'),
            'city' => Yii::t('XcoinModule.forms_CashOutForm', 'City'),
            'zipCode' => Yii::t('XcoinModule.forms_CashOutForm', 'Zip Code'),
        ];
    }

    public function attributeHints()
    {
        return [
            'amount' => Yii::t('XcoinModule.forms_CashOutForm', 'Minimum : 10 coins / Maximum : ' . $this->getCoinBalance() . ' coins'),
        ];
    }

    public function makeTransaction()
    {
        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
        $transaction->from_account_id = $this->senderAccount->id;
        $transaction->to_account_id = $this->cashOutAccount->id;
        $transaction->asset_id = $this->cashOutAsset->id;
        $transaction->amount = $this->amount;

        if (!$transaction->save()) {
            $message = implode(' ', array_map(function ($errors) {
                return implode(' ', $errors);
            }, $transaction->getErrors()));

            throw new ServerErrorHttpException(sprintf('Could not save transaction due to : [%s]', $message));
        }

        $this->cashoutTransaction = $transaction;
    }

    public function getCashoutSpace()
    {
        return $this->cashOutSpace;
    }

    public function getCashOutAsset()
    {
        return $this->cashOutAsset;
    }

    public function getCashOutAccount()
    {
        return $this->cashOutAccount;
    }

    public function getCashOutTransaction()
    {
        return $this->cashoutTransaction;
    }

    public function getCashOutAssetName()
    {
        return $this->cashOutAssetName;
    }

    public function getCashoutBridge()
    {
        return $this->cashOutBridge;
    }

    public function getCoinBalance()
    {
        return $this->senderAccount->getAssetBalance($this->cashOutAsset);
    }

    public function getCashoutBridgeRedirectDate()
    {
        /** @var User $user */
        $user = Yii::$app->user->identity;

        return [
            'fullName' => $this->accountOwner,
            'email' => $user->email,
            'country' => $this->country,
            'address' => $this->address,
            'state' => $this->state,
            'city' => $this->city,
            'postCode' => $this->zipCode,
            'coin' => $this->cashOutAssetName,
            'transactionHash' => $this->cashoutTransaction->algorand_tx_id,
            'amount' => $this->amount,
            'redirectUrl' => Url::toRoute(['/xcoin/overview', 'contentContainer' => $user], true) . '?res=success',
            'beneficiary' => $this->cashOutAccount->title,
            'iban' => $this->iban,
            'bic' => $this->iban,
            'swift' => $this->swift,
        ];
    }
}
