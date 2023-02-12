<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2021 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Mortadha Ghanmi <mortadha.ghanmi56@gmail.com>
 */

namespace humhub\modules\xcoin\models\forms;

use humhub\modules\user\models\Profile;
use humhub\modules\user\models\ProfileField;
use humhub\modules\xcoin\models\PurchaseTransaction;
use humhub\modules\xcoin\models\Transaction;
use Yii;
use yii\base\Model;
use yii\web\IdentityInterface;

/**
 * This is the model form class for Purchase form.
 *
 * @property string $coin
 * @property integer $amount
 * @property string $address
 * @property string $state
 * @property string $city
 * @property integer $zip
 * @property string $country
 * 
 * @property IdentityInterface|null $user
 * 
 * @property ProfileField $addressField
 * @property ProfileField $stateField
 * @property ProfileField $cityField
 * @property ProfileField $zipField
 * @property ProfileField $countryField
 */
class PurchaseForm extends Model
{
    public $coin;
    public $amount;
    public $address;
    public $state;
    public $city;
    public $zip;
    public $country;
    public $transaction;

    private $user;

    private $addressField;
    private $stateField;
    private $cityField;
    private $zipField;
    private $countryField;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->user = Yii::$app->user->getIdentity();

        $this->addressField = ProfileField::find()->where(['internal_name' => 'street'])->one();
        $this->stateField = ProfileField::find()->where(['internal_name' => 'state'])->one();
        $this->cityField = ProfileField::find()->where(['internal_name' => 'city'])->one();
        $this->zipField = ProfileField::find()->where(['internal_name' => 'zip'])->one();
        $this->countryField = ProfileField::find()->where(['internal_name' => 'country'])->one();

        $this->coin = Yii::$app->params['coinPurchase']['coin'];
        $this->address = $this->addressField->getUserValue($this->user);
        $this->state = $this->stateField->getUserValue($this->user);
        $this->city = $this->cityField->getUserValue($this->user);
        $this->zip = $this->zipField->getUserValue($this->user);
        $this->country = $this->countryField->getUserValue($this->user);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $addressFieldRules = $this->addressField->getFieldType()->getFieldRules()[0];
        $addressFieldRules[0] = 'address';

        return [
            [['coin', 'amount', 'address', 'state', 'city', 'zip', 'country'], 'required'],
            ['amount', 'integer', 'min' => 10],
            $addressFieldRules,
            $this->stateField->getFieldType()->getFieldRules()[0],
            $this->cityField->getFieldType()->getFieldRules()[0],
            $this->zipField->getFieldType()->getFieldRules()[0],
            $this->countryField->getFieldType()->getFieldRules()[0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        // TODO: retrieve dynamic labels from profile fields
        return [
            'coin' => Yii::t('XcoinModule.forms_PurchaseForm', 'Selected COIN'),
            'amount' => Yii::t('XcoinModule.forms_PurchaseForm', 'Number of COINs'),
            'address' => Yii::t('XcoinModule.forms_PurchaseForm', 'Address'),
            'state' => Yii::t('XcoinModule.forms_PurchaseForm', 'State'),
            'city' => Yii::t('XcoinModule.forms_PurchaseForm', 'City'),
            'zip' => Yii::t('XcoinModule.forms_PurchaseForm', 'Postcode'),
            'country' => Yii::t('XcoinModule.forms_PurchaseForm', 'Country'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $profile = Profile::findOne(['user_id' => $this->user->getId()]);
        
        $profile->street = $this->address;
        $profile->state = $this->state;
        $profile->city = $this->city;
        $profile->zip = $this->zip;
        $profile->country = $this->country;

        return $profile->validate() && $profile->save();
    }

    public function saveHolderTransaction($assetId, $toAccountId, $fromAccountId)
    {
        // save holder transaction
        $transaction = new PurchaseTransaction();

        $transaction->asset_id = $assetId;
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_COPY;
        $transaction->to_account_id = $toAccountId;
        $transaction->from_account_id = $fromAccountId;
        $transaction->amount = $this->amount;
        $transaction->key = uniqid();

        $transaction->save();

        $this->transaction = $transaction;
    }
}
