<?php
/**
 * Created by Safouane Fakhfakh.
 * Email: Safouane.Fakhfakh@mail.com
 */

namespace humhub\modules\xcoin\models;


use Yii;
use yii\base\Model;

class SpaceModuleBasicSettings extends Model
{
    /**
     * @var string Title of the new member account
     */
    public $accountTitle;
    /**
     * @var float Amount of the transaction to new account joining the space
     */
    public $transactionAmount;
    /**
     * @var string Comment of the transaction to new account joining the space
     */
    public $transactionComment;

    /**
     * @var boolean
     */
    public $allowDirectCoinTransfer;

    public function init()
    {
        $module = Yii::$app->getModule('xcoin');
        $this->accountTitle = $module->settings->space()->get('accountTitle');
        $this->transactionAmount = $module->settings->space()->get('transactionAmount');
        $this->transactionComment = $module->settings->space()->get('transactionComment');

        if (null !== $allowDirectCoinTransfer = $module->settings->space()->get('allowDirectCoinTransfer'))
            $this->allowDirectCoinTransfer = $allowDirectCoinTransfer;
        else
            $this->allowDirectCoinTransfer = 1;
    }

    public function showAccountTitle()
    {
        return $this->accountTitle;
    }

    public function showTransactionAmount()
    {
        return $this->transactionAmount;
    }

    public function showTransactionComment()
    {
        return $this->transactionComment;
    }

    public function showAllowDirectCoinTransfer()
    {
        return $this->allowDirectCoinTransfer;
    }

    /**
     * Static initializer
     * @return \self
     */
    public static function instantiate()
    {
        return new self;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['accountTitle', 'transactionComment'], 'required'],
            ['transactionAmount', 'number', 'min' => 1],
            ['allowDirectCoinTransfer', 'boolean']
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'accountTitle' => Yii::t('XcoinModule.config', 'Account title'),
            'transactionAmount' => Yii::t('XcoinModule.config', 'Transaction amount'),
            'transactionComment' => Yii::t('XcoinModule.config', 'Transaction comment'),
            'allowDirectCoinTransfer' => Yii::t('XcoinModule.config', 'Allow direct coin transfer'),
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }
        $module = Yii::$app->getModule('xcoin');
        $module->settings->space()->set('accountTitle', $this->accountTitle);
        $module->settings->space()->set('transactionAmount', $this->transactionAmount);
        $module->settings->space()->set('transactionComment', $this->transactionComment);
        $module->settings->space()->set('allowDirectCoinTransfer', $this->allowDirectCoinTransfer);
        return true;

    }

}