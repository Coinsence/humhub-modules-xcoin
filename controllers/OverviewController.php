<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\algorand\calls\Coin;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\Transaction;
use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\forms\PurchaseForm;
use yii\helpers\Url;

/**
 * Description of AccountController
 *
 * @author Luke
 * @author gdaly
 */
class OverviewController extends ContentContainerController
{

    public function actionIndex()
    {
        $callback = Yii::$app->request->get('res');
        $key = Yii::$app->request->get('key');

        if ($callback === 'success' && !empty($key)) {
            Transaction::copyTransaction($key);
        }

        if ($this->contentContainer instanceof Space) {
            return $this->render('index_space', [
                'asset' => AssetHelper::getSpaceAsset($this->contentContainer)
            ]);
        } else {
            return $this->render('index_profile', [
                'isOwner' => ($this->contentContainer->id === Yii::$app->user->id)
            ]);
        }
    }

    public function actionLatestTransactions()
    {
        return $this->render('latest-transactions');
    }

    public function actionLatestAssetTransactions()
    {
        return $this->render('latest-asset-transactions', ['asset' => AssetHelper::getSpaceAsset($this->contentContainer)]);
    }

    public function actionShareholderList()
    {
        return $this->render('shareholder-list', ['asset' => AssetHelper::getSpaceAsset($this->contentContainer)]);
    }

    public function actionPurchaseCoin($fundingId = null)
    {
        $form = new PurchaseForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate() && $form->save()) {
            $this->view->saved();
            $bridge = Yii::$app->params['coinPurchase']['bridge'];
            $defaultAccount = Account::findOne([
                'user_id' => $this->contentContainer->id,
                'account_type' => Account::TYPE_DEFAULT
            ]);

            $space = Space::findOne(['name' => $form->coin]);
            $spaceAsset = Asset::findOne(['space_id' => $space->id]);

            $defaultSpaceAccount = Account::findOne([
                'space_id' => $space->id,
                'account_type' => Account::TYPE_DEFAULT
            ]);

            // ensure minimum algo balance for default account
            Coin::optinCoin($defaultAccount, $spaceAsset->algorand_asset_id);

            $form->saveHolderTransaction($spaceAsset->id, $defaultAccount->id, $defaultSpaceAccount->id);

            if ($fundingId) {
                $funding = Funding::find()->where(['id' => $fundingId])->one();
                $redirectUrl = Url::toRoute(['/xcoin/funding/overview', "fundingId" => $fundingId, 'contentContainer' => $funding->space], true) . '?res=success&key=' . $form->transaction->key;
            } else {
                $redirectUrl = Url::toRoute(['/xcoin/overview', 'contentContainer' => $this->contentContainer], true) . '?res=success&key=' . $form->transaction->key;
            }
            $data = [
                'fullName' => $this->contentContainer->displayName,
                'email' => $this->contentContainer->email,
                'address' => $form->address,
                'state' => $form->state,
                'city' => $form->city,
                'postCode' => $form->zip,
                'country' => $form->country,
                'coin' => $form->coin,
                'amount' => intval($form->amount),
                'pAddress' => $defaultAccount->algorand_address,
                'accountId' => $defaultAccount->guid,
                'redirectUrl' => $redirectUrl,
            ];

            $jsonData = json_encode($data);

            $encodedData = base64_encode($jsonData);
            $this->redirect($bridge . '?data=' . $encodedData);
        }

        return $this->renderAjax('purchase-coin-prompt', [
            'coin' => $form->coin,
            'model' => $form
        ]);
    }
}
