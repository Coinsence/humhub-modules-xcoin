<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\content\models\ContentContainer;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\models\User;
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

        if ($transaction->load(Yii::$app->request->post()) && $transaction->save()) {
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




}
