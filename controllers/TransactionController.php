<?php

namespace humhub\modules\xcoin\controllers;

use humhub\components\Event;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Transaction;
use Yii;
use yii\web\HttpException;

/**
 * Description of AccountController
 *
 * @author Luke
 */
class TransactionController extends ContentContainerController
{
    protected $idProductttt;
    public function actionIndex()
    {
        return $this->actionSelectAccount();
    }

    public function actionSelectAccount()
    {
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

        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
        $transaction->from_account_id = $fromAccount->id;
        $transaction->asset_id = array_keys($accountAssetList)[0];

        if ($transaction->load(Yii::$app->request->post())) {

            // disable transfer to ISSUE ACCOUNT
            if ($this->contentContainer instanceof Space) {
                if (AccountHelper::getIssueAccount($this->contentContainer)->id == Account::findOne(['id' => $transaction->to_account_id])->id) {
                    throw new HttpException(401, 'Can\'t transfer back coins to ISSUE ACCOUNT');
                }
            }

            $transaction->save();

            $this->view->saved();

            return $this->htmlRedirect([
                '/xcoin/account',
                'id' => $transaction->from_account_id,
                'container' => $this->contentContainer
            ]);
        }

        return $this->renderAjax('transfer', [
            'transaction' => $transaction,
            'fromAccount' => $fromAccount,
            'accountAssetList' => $accountAssetList
        ]);
    }

    public function actionDetails($id)
    {
        $transaction = Transaction::findOne(['id' => $id]);

        return $this->renderAjax('details', ['transaction' => $transaction]);
    }

    public function actionSelectAccountPayment($productId)
    {
        $this->idProductttt=$productId;
     
        return $this->renderAjax('select-account-payment', [
            'contentContainer' => $this->contentContainer,
            'id'=>$this->idProductttt.'11',
            'nextRoute' => ['/xcoin/transaction/transfer1', 'contentContainer' => $this->contentContainer,'id'=>$this->idProductttt]
        ]);
    }

    public function actionTransfer1($accountId,$id)
    {
       
        $fromAccount = Account::findOne(['id' => $accountId]);
        $dbCommand = Yii::$app->db->createCommand("
        select *
         from xcoin_product 
        where id=".$id.";");
        $data = $dbCommand->queryAll();//output
        
        
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

        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
        $transaction->from_account_id = $fromAccount->id;
        $transaction->asset_id = array_keys($accountAssetList)[0];

        if ($transaction->load(Yii::$app->request->post())) {

            // disable transfer to ISSUE ACCOUNT
            if ($this->contentContainer instanceof Space) {
                if (AccountHelper::getIssueAccount($this->contentContainer)->id == Account::findOne(['id' => $transaction->to_account_id])->id) {
                    throw new HttpException(401, 'Can\'t transfer back coins to ISSUE ACCOUNT');
                }
            }

            $transaction->save();

            $this->view->saved();

            return $this->htmlRedirect([
                '/xcoin/account',
                'id' => $transaction->from_account_id,
                'container' => $this->contentContainer
            ]);
        }

        return $this->renderAjax('transfer1', [
            'transaction' => $transaction,
            'fromAccount' => $fromAccount,
            'accountAssetList' => $accountAssetList,
            'idProduct1'=>$id,
            'product'=>$data
        ]);
    }



}
