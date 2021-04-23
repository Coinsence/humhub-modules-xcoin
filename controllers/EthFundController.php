<?php


namespace humhub\modules\xcoin\controllers;

use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\Transaction;
use Yii;
use yii\console\Controller;
use yii\web\HttpException;

/**
 * Correct investor transactions amount an round them to 35coins rule
 *
 * @author Ghaith Daly <ghaith.daly@beecoop.co>
 */
class EthFundController extends Controller
{
    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (!$this->module->isCrowdfundingEnabled()) {
            throw new HttpException(403, Yii::t('XcoinModule.base', 'Crowdfunding is not enabled'));
        }

        return true;
    }

    public function actionCorrectVotes()
    {
        $challenge = Challenge::findOne(['id' => 7]); // SDG Impact Challenge
        $challengeAsset = $challenge->getAsset()->one();

        foreach ($challenge->getFundings()->all() as /** @var Funding $funding */ $funding) {

            /** @var Account $account */
            $account = $funding->getFundingAccount();

            /** @var Space $fundingSpace */
            $fundingSpace = $funding->getSpace()->one();

            /** @var Asset $spaceAsset */
            $spaceAsset = Asset::findOne(['space_id' => $fundingSpace->id]);

            /** @var Transaction[] $incomingTransactions */
            $incomingTransactions = $account->getTransactionsTo()->all();

            foreach ($incomingTransactions as /** @var Transaction */ $transaction) {
                if ($transaction->asset == $challengeAsset && $transaction->transaction_type == Transaction::TRANSACTION_TYPE_TRANSFER && $transaction->amount > 35) {
                    $amountToRevert = $transaction->amount - 35;

                    $outGoingTransaction = new Transaction();
                    $outGoingTransaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
                    $outGoingTransaction->asset_id = $transaction->asset_id;
                    $outGoingTransaction->to_account_id = $transaction->from_account_id;
                    $outGoingTransaction->from_account_id = $transaction->to_account_id;
                    $outGoingTransaction->amount = $amountToRevert;
                    $outGoingTransaction->comment = Yii::t('XcoinModule.base', 'Revert Funding Invest');
                    if (!$outGoingTransaction->save()) {
                        print_r($outGoingTransaction->getErrors(), 1);
                        throw new HttpException(Yii::t('XcoinModule.base', 'Transaction failed!'));
                    }

                    $inComingTransaction = new Transaction();
                    $inComingTransaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
                    $inComingTransaction->asset_id = $spaceAsset->id;
                    $inComingTransaction->to_account_id = $transaction->to_account_id;
                    $inComingTransaction->from_account_id = $transaction->from_account_id;
                    $inComingTransaction->amount = $amountToRevert;
                    $inComingTransaction->comment = Yii::t('XcoinModule.base', 'Revert Funding Invest');
                    if (!$inComingTransaction->save()) {
                        print_r($inComingTransaction->getErrors(), 1);
                        throw new HttpException(Yii::t('XcoinModule.base', 'Transaction failed!'));
                    }
                }
            }
        }
    }
}