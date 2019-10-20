<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Mortadha Ghanmi <mortadba.ghanmi56@gmail.com>
 */

namespace humhub\modules\xcoin\jobs;


use humhub\modules\queue\ActiveJob;
use humhub\modules\xcoin\component\Utils;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Transaction;
use Yii;

class IssueScheduledTransactions extends ActiveJob
{

    const TRANSACTION_PERIOD_NONE = -1;
    const TRANSACTION_PERIOD_WEEKLY = 0;
    const TRANSACTION_PERIOD_MONTHLY = 1;

    const SCHEDULE_DELAY_WEEKLY = 3600 * 24 * 7;
    const SCHEDULE_DELAY_MONTHLY = 3600 * 24 * 7 * 4;

    /**
     * @var \humhub\modules\space\models\Space Space on which these settings are for
     */
    public $space;

    /**
     * This will make the job re-executed again in the future like a cron
     */
    private function cron($seconds) {

        $module = Yii::$app->getModule('xcoin');
        $scheduleJobId = Yii::$app->queue->delay($seconds)->push(new IssueScheduledTransactions(['space' => $this->space]));
        $module->settings->contentContainer($this->space)->set('scheduleJobId', $scheduleJobId);

    }

    private function issueCoins() {

        $module = Yii::$app->getModule('xcoin');

        $transactionAmount = $module->settings->contentContainer($this->space)->get('transactionAmount');
        $transactionComment = $module->settings->contentContainer($this->space)->get('transactionComment');

        $spaceIssueAccount = AccountHelper::getIssueAccount($this->space);
        $spaceDefaultAccount = Account::findOne(['space_id' => $this->space->id, 'account_type' => Account::TYPE_DEFAULT]);

        //Exit if module settings are not set or space default account or issue account are not set
        if (!$transactionAmount || !$transactionComment || !$spaceIssueAccount || !$spaceDefaultAccount) {
            return;
        }

        $memberAccounts = Account::findAll([
            'space_id' => $this->space->id,
            'account_type' => Account::TYPE_COMMUNITY_INVESTOR
        ]);

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

    /**
     * Issue the scheduled space transaction each set period of time
     */
    public function run()
    {

        $module = Yii::$app->getModule('xcoin');

        // The coin allocation logic
        $this->issueCoins();

        $transactionPeriod = $module->settings->contentContainer($this->space)->get('transactionPeriod');
        switch ($transactionPeriod) {
            case Utils::TRANSACTION_PERIOD_NONE:
                break;
            case Utils::TRANSACTION_PERIOD_WEEKLY:
                $this->cron(Utils::SCHEDULE_DELAY_WEEKLY);
                break;
            case Utils::TRANSACTION_PERIOD_MONTHLY:
                $this->cron(Utils::SCHEDULE_DELAY_MONTHLY);
                break;
            default:
                break;
        }
    }
}