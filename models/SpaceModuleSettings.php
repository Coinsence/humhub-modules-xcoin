<?php
/**
 * Created by Safouane Fakhfakh.
 * Email: Safouane.Fakhfakh@mail.com
 */

namespace humhub\modules\xcoin\models;


use Yii;
use yii\base\Model;

class SpaceModuleSettings extends Model
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

    public function init()
    {
        $module = Yii::$app->getModule('xcoin');
        $this->accountTitle = $module->settings->space()->get('accountTitle');
        $this->transactionAmount = $module->settings->space()->get('transactionAmount');
        $this->transactionComment = $module->settings->space()->get('transactionComment');
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
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'accountTitle' => Yii::t('XcoinModule.base', 'Account title'),
            'transactionAmount' => Yii::t('XcoinModule.base', 'Transaction amount'),
            'transactionComment' => Yii::t('XcoinModule.base', 'Transaction comment'),
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
        return true;

    }

}