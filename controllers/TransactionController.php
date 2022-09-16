<?php

namespace humhub\modules\xcoin\controllers;

use Da\QrCode\QrCode;
use humhub\modules\algorand\calls\Coin;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\forms\CashOutForm;
use humhub\modules\xcoin\models\Transaction;
use humhub\modules\xcoin\models\Product;
use Yii;
use yii\web\HttpException;
use yii\web\ServerErrorHttpException;

/**
 * Description of AccountController
 *
 * @author Luke
 * @author gdaly <daly.ghaith@gmail.com>
 */
class TransactionController extends ContentContainerController
{
    public function actionIndex()
    {
        return $this->actionSelectAccount();
    }

    public function actionSelectAccount($productId = null, $assetId = null)
    {
        if (null !== $assetId) {
            if (null === $asset = Asset::findOne(['id' => $assetId])) {
                throw new HttpException(404);
            }

            return $this->renderAjax('select-account', [
                'contentContainer' => $this->contentContainer,
                'requireAsset' => $asset,
                'nextRoute' => ['/xcoin/transaction/cash-out', 'contentContainer' => $this->contentContainer],
                'isCoinCashOut' => true
            ]);
        }

        if ($productId !== null) {
            $product = Product::findOne(['id' => $productId]);
            if ($product === null) {
                throw new HttpException(404);
            }

            return $this->renderAjax('select-account', [
                'contentContainer' => $this->contentContainer,
                'requireAsset' => $product->marketplace->asset,
                'product' => $product,
                'nextRoute' => ['/xcoin/transaction/pay', 'contentContainer' => $this->contentContainer]
            ]);
        }

        return $this->renderAjax('select-account', [
            'contentContainer' => $this->contentContainer,
            'nextRoute' => ['/xcoin/transaction/transfer', 'contentContainer' => $this->contentContainer]
        ]);
    }

    public function actionTransfer($accountId)
    {
        $fromAccount = Account::findOne(['id' => $accountId]);
        if ($fromAccount === null) {
            throw new HttpException(404);
        }

        if (!AccountHelper::canManageAccount($fromAccount)) {
            throw new HttpException(401);
        }

        $accountAssetList = [];
        foreach ($fromAccount->getAssets() as $asset) {
            $max = $fromAccount->getAssetBalance($asset);
            if (!empty($max)) {
                $accountAssetList[$asset->id] = SpaceImage::widget([
                        'space' => $asset->space,
                        'width' => 16,
                        'showTooltip' => true,
                        'link' => true])
                    . ' ' . $asset->space->name . '<small class="pull-rightx"> - max. ' . $max . '</small>';
            }
        }

        if (empty($accountAssetList)) {
            throw new HttpException(404, 'No assets available on this account!');
        }

        if (Yii::$app->request->isPost && Yii::$app->request->post('overview') == '1') {
            return $this->htmlRedirect([
                '/xcoin/account',
                'id' => $fromAccount->id,
                'container' => $this->contentContainer
            ]);
        }

        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
        $transaction->from_account_id = $fromAccount->id;
        $transaction->asset_id = array_keys($accountAssetList)[0];

        $qrCode = (new QrCode($fromAccount->guid))
            ->setSize(150);

        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '1') {
            return $this->renderAjax('transfer-select-coin', [
                'transaction' => $transaction,
                'accountAssetList' => $accountAssetList
            ]);
        }

        $transaction->load(Yii::$app->request->post());

        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '2') {
            if($transaction->amount == null){
                $transaction->addError('amount','this field is required');
                return $this->renderAjax('transfer-select-coin', [
                    'transaction' => $transaction,
                    'accountAssetList' => $accountAssetList
                ]);
            }
            return $this->renderAjax('transfer-select-sender-account', [
                'transaction' => $transaction,
                'accountAssetList' => $accountAssetList
            ]);
        }

        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '3') {
            if($transaction->toAccount == null){
               $transaction->addError('to_account_id','this field is required');
                return $this->renderAjax('transfer-select-sender-account', [
                    'transaction' => $transaction,
                    'accountAssetList' => $accountAssetList
                ]);
            }
            // disable transfer to ISSUE ACCOUNT
            if ($this->contentContainer instanceof Space) {
                if (AccountHelper::getIssueAccount($this->contentContainer)->id == Account::findOne(['id' => $transaction->to_account_id])->id) {
                    throw new HttpException(401, 'Can\'t transfer back coins to ISSUE ACCOUNT');
                }
            }
            $transaction->save();
            $this->view->saved();

            return $this->renderAjax('transfer-overview', [
                'transaction' => $transaction,
                'asset' => Asset::findOne(['id' => $transaction->asset_id]),
            ]);
        }
        // Check validation


        return $this->renderAjax('transfer', [
            'transaction' => $transaction,
            'fromAccount' => $fromAccount,
            'qrCode'=>$qrCode
        ]);
    }

    public function actionPay($accountId, $productId)
    {
        $fromAccount = Account::findOne(['id' => $accountId]);
        if ($fromAccount === null) {
            throw new HttpException(404);
        }

        if (!AccountHelper::canManageAccount($fromAccount)) {
            throw new HttpException(401);
        }

        $product = Product::findOne(['id' => $productId]);
        if ($product === null) {
            throw new HttpException(404);
        }

        $seller = $product->getCreatedBy()->one();

        if ($product->isSpaceProduct()) {
            $toAccount = Account::findOne(['space_id' => $product->space->id, 'account_type' => Account::TYPE_DEFAULT]);
        } else {
            $toAccount = Account::findOne(['user_id' => $seller->id, 'account_type' => Account::TYPE_DEFAULT, 'space_id' => null]);
        }

        if ($toAccount === null) {
            throw new HttpException(404);
        }

        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
        $transaction->from_account_id = $fromAccount->id;
        $transaction->to_account_id = $toAccount->id;
        $transaction->asset_id = $product->marketplace->asset_id;
        $transaction->amount = $product->price;

        if (Yii::$app->request->isPost) {

            if ($fromAccount->getAssetBalance($product->marketplace->asset) < $product->price) {
                $this->view->error(Yii::t('XcoinModule.transaction', 'Insufficient balance.'));

                return $this->htmlRedirect($seller->createUrl('/xcoin/product/overview', ['productId' => $product->id]));
            }

            $transaction->save();
            $this->view->saved();

            if ($product->marketplace->shouldRedirectToLink()) {
                if ($product->link) {
                    return $this->redirect($product->link);
                }

                return $this->redirect($seller->createUrl('/xcoin/product/overview', ['productId' => $product->id]));
            }

            return $this->htmlRedirect(['/xcoin/product/buy', 'container' => Yii::$app->user->identity, 'productId' => $product->id]);
        }

        return $this->renderAjax('pay', [
            'transaction' => $transaction,
            'product' => $product
        ]);
    }

    public function actionDetails($id)
    {
        $transaction = Coin::transaction($id);

        return $this->renderAjax('details', ['transaction' => $transaction]);
    }

    public function actionCashOut($accountId)
    {
        $senderAccount = Account::findOne(['id' => $accountId]);

        if (null === $senderAccount) {
            throw new HttpException(404);
        }

        $cashOutForm = new CashOutForm();
        $cashOutForm->senderAccount = $senderAccount;

        if ($cashOutForm->load(Yii::$app->request->post()) && $cashOutForm->validate()) {

            try {
                $cashOutForm->makeTransaction();
            } catch (ServerErrorHttpException $exception) {
                $this->view->error($exception->getMessage());
            }

            $jsonData = json_encode($cashOutForm->getCashoutBridgeRedirectDate());

            $encodedData = base64_encode($jsonData);
            $this->redirect($cashOutForm->getCashoutBridge() . '?data=' . $encodedData);

            $this->view->saved();
        }

        return $this->renderAjax('cashout-coin-prompt', [
            'cashOutAssetName' => $cashOutForm->getCashOutAssetName(),
            'model' => $cashOutForm
        ]);
    }
}
