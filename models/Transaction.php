<?php

namespace humhub\modules\xcoin\models;

use humhub\components\Event;
use humhub\modules\xcoin\helpers\Utils;
use Yii;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\helpers\AssetHelper;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "xcoin_transaction".
 *
 * @property integer $id
 * @property integer $asset_id
 * @property integer $transaction_type
 * @property integer $to_account_id
 * @property integer $from_account_id
 * @property integer $amount
 * @property string $comment
 * @property string $algorand_tx_id
 *
 * @property Asset $asset
 * @property Account $fromAccount
 * @property Account $toAccount
 */
class Transaction extends ActiveRecord
{

    const TRANSACTION_TYPE_TRANSFER = 1;
    const TRANSACTION_TYPE_ISSUE = 2;
    const TRANSACTION_TYPE_TASK_PAYMENT = 3;
    const TRANSACTION_TYPE_REVERT = 4;
    const TRANSACTION_TYPE_ALLOCATE = 5;

    /** @var Event this event is dispatched a transaction with TRANSACTION_TYPE_ISSUE is triggered
     */
    const EVENT_TRANSACTION_TYPE_ISSUE = 'transactionTypeIssue';

    /** @var Event this event is dispatched a transaction with TRANSACTION_TYPE_ISSUE is triggered
     */
    const EVENT_TRANSACTION_TYPE_TRANSFER = 'transactionTypeTransfer';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_transaction';
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
            [['comment'], 'string', 'max' => 200],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::className(), 'targetAttribute' => ['asset_id' => 'id']],
            [['from_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['from_account_id' => 'id']],
            [['to_account_id'], 'exist', 'skipOnError' => true, 'targetClass' => Account::className(), 'targetAttribute' => ['to_account_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('XcoinModule.base', 'ID'),
            'asset_id' => Yii::t('XcoinModule.base', 'Asset'),
            'transaction_type' => Yii::t('XcoinModule.base', 'Transaction Type'),
            'to_account_id' => Yii::t('XcoinModule.base', 'Recipient'),
            'from_account_id' => Yii::t('XcoinModule.base', 'From Account ID'),
            'amount' => Yii::t('XcoinModule.base', 'Amount'),
            'comment' => Yii::t('XcoinModule.base', 'Comment'),
        ];
    }

    public function beforeValidate()
    {

        // Check amount decimal points (max 4)
        $this->amount = str_replace(',', '.', $this->amount);
        if (strlen(substr(strrchr($this->amount, "."), 1)) > 4) {
            $this->addError('amount', Yii::t('XcoinModule.base', 'Maximum 4 decimal places.'));
        }
        $this->amount = round($this->amount, 4);

        if ($this->transaction_type === self::TRANSACTION_TYPE_TRANSFER) {

            // Check Issue Account Asset Type
            if ($this->toAccount->account_type == Account::TYPE_ISSUE) {
                $asset = AssetHelper::getSpaceAsset($this->toAccount->space);
                if ($asset->id != $this->asset_id) {
                    $this->addError('asset_id', Yii::t('XcoinModule.base', 'You cannot transfer this type of asset.'));
                }
            }

            // Check sender account balance
            if ($this->fromAccount->getAssetBalance($this->asset) < $this->amount) {
                $this->addError('asset_id', Yii::t('XcoinModule.base', 'Insuffient funds.'));
            }
        }

        if ($this->from_account_id == $this->to_account_id) {
            $this->addError('from_account_id', Yii::t('XcoinModule.base', 'Please select an different account!'));
            $this->addError('to_account_id', Yii::t('XcoinModule.base', 'Please select an different account!'));
        }


        return parent::beforeValidate();
    }

    public function beforeSave($insert)
    {
        if ($this->transaction_type != self::TRANSACTION_TYPE_ISSUE && $this->algorand_tx_id == null) {
            Event::trigger(Transaction::class, Transaction::EVENT_TRANSACTION_TYPE_TRANSFER, new Event(['sender' => $this]));
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
