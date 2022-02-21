<?php

namespace humhub\modules\xcoin\controllers;

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

    public function actionPurchaseCoin($spaceId = null)
    {
        $form = new PurchaseForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate() && $form->save()) {
            $this->view->saved();
            $bridge = Yii::$app->params['coinPurchase']['bridge'];
            $defaultAccount = Account::findOne([
                'user_id' => $this->contentContainer->id,
                'account_type' => Account::TYPE_DEFAULT
            ]);
            if ($spaceId) {
                $space = Space::find()->where(['id' => $spaceId])->one();
                $redirectUrl = Url::toRoute(['/xcoin/funding', 'contentContainer' => $space], true) . '?res=success';
            } else {
                $redirectUrl = Url::toRoute(['/xcoin/overview', 'contentContainer' => $this->contentContainer], true) . '?res=success';
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
                'pAddress' => $defaultAccount->ethereum_address,
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
