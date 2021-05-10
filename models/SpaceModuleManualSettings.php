<?php
/**
 * Created by Mortadha Ghanmi.
 * Email: mortadha.ghanmi56@gmail.com
 */

namespace humhub\modules\xcoin\models;


use humhub\components\Event;
use humhub\modules\space\models\Space;
use self;
use Yii;
use yii\base\BaseObject;
use yii\base\Model;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\helpers\AssetHelper;

class SpaceModuleManualSettings extends Model
{
    /**
     * @var boolean
     */
    public $selectAllMembers;

    /**
     * @var array Array of selected space members
     */
    public $selectedMembers;

    /**
     * @var Space Space on which these settings are for
     */
    public $space;

    public function init() {

    }

    /**
     * Static initializer
     * @return self
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
            [['selectAllMembers'], 'boolean'],
            [['selectedMembers'], 'required', 'when' => function ($model) {
                return $model->selectAllMembers == false;
            }, 'whenClient' => "function (attribute, value) {
                return $('#spacemodulemanualsettings-selectAllMembers').val() == 0;
            }"],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'selectAllMembers' => Yii::t('XcoinModule.config', 'Select all members'),
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $this->manualAllocation();


        return true;

    }

    private function manualAllocation() {

        $module = Yii::$app->getModule('xcoin');

        $transactionAmount = $module->settings->contentContainer($this->space)->get('transactionAmount');
        $transactionComment = $module->settings->contentContainer($this->space)->get('transactionComment');

        $spaceIssueAccount = AccountHelper::getIssueAccount($this->space);
        $spaceDefaultAccount = Account::findOne(['space_id' => $this->space->id, 'account_type' => Account::TYPE_DEFAULT]);

        //Exit if module settings are not set or space default account or issue account are not set
        if (!$transactionAmount || !$transactionComment || !$spaceIssueAccount || !$spaceDefaultAccount) {
            return;
        }

        if ($this->selectAllMembers) {
            $memberAccounts = Account::findAll([
                'space_id' => $this->space->id,
                'account_type' => Account::TYPE_COMMUNITY_INVESTOR
            ]);
        }
        else {
            $userIds = array_map(function ($guid) { return User::findOne(['guid' => $guid])->id; }, $this->selectedMembers);

            $memberAccounts = array_map(function ($id) {
                return Account::findOne([
                    'user_id' => $id,
                    'space_id' => $this->space->id,
                    'account_type' => Account::TYPE_COMMUNITY_INVESTOR
                ]);
            }, $userIds);
        }

        foreach ($memberAccounts as $memberAccount) {

            // Calculate difference to $transactionAmount
            $currentBalance = $memberAccount->getAssetBalance(AssetHelper::getSpaceAsset($this->space));
            if ($currentBalance >= $transactionAmount)
                continue;
            $newTransactionAmount = $transactionAmount - $currentBalance;

            // Issue transaction amount to default account
            $issueTransaction = new Transaction();
            $issueTransaction->amount = $newTransactionAmount;
            $issueTransaction->from_account_id = $spaceIssueAccount->id;
            $issueTransaction->to_account_id = $spaceDefaultAccount->id;
            $issueTransaction->asset_id = AssetHelper::getSpaceAsset($this->space)->id;
            $issueTransaction->transaction_type = Transaction::TRANSACTION_TYPE_ISSUE;
            $issueTransaction->comment = "Issue transaction Amount to default Account";
            if (!$issueTransaction->save()) {
                Yii::error("can't issue this Amount !, transaction: " . json_encode($issueTransaction));
            }

            Event::trigger(Transaction::class, Transaction::EVENT_TRANSACTION_TYPE_ISSUE, new Event(['sender' => $issueTransaction]));

            // New member account transaction
            $transferTransaction = new Transaction();
            $transferTransaction->amount = $newTransactionAmount;
            $transferTransaction->from_account_id = $spaceDefaultAccount->id;
            $transferTransaction->to_account_id = $memberAccount->id;
            $transferTransaction->asset_id = AssetHelper::getSpaceAsset($this->space)->id;
            $transferTransaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
            $transferTransaction->comment = $transactionComment;
            if (!$transferTransaction->save()) {
                Yii::error("Can't transfer transaction amount to member account, transaction: " . json_encode($transferTransaction));
            }

        }

    }

}