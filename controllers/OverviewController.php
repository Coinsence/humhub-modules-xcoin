<?php

namespace humhub\modules\xcoin\controllers;

use humhub\components\Event;
use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Account;
use yii\base\DynamicModel;
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

    public function actionPurchaseCoin()
    {
        $amountModel = new DynamicModel(['amount']);
        $amountModel->addRule(['amount'], 'integer', ['min' => 0]);
        $amountModel->addRule(['amount'], 'required');

        if ($amountModel->load(Yii::$app->request->post()) && $amountModel->validate()) {
            $bridge = Yii::$app->params['coinPurchase']['bridge'];
            $defaultAccount = Account::findOne([
                'user_id' => $this->contentContainer->id,
                'account_type' => Account::TYPE_DEFAULT
            ]);

            $data = [
                'fullName' => $this->contentContainer->displayName,
                'coin' => 'coinsence',
                'amount' => intval($amountModel->amount),
                'pAddress' => $defaultAccount->ethereum_address,
                'rediectUrl' => Url::toRoute(['/xcoin/overview', 'contentContainer' => $this->contentContainer], true) . '?res=success',
            ];
            
            $jsonData = json_encode($data);
            $encodedData = base64_encode($jsonData);
            $this->redirect($bridge . '?data=' . $encodedData);
        }
        $coin = Yii::$app->params['coinPurchase']['coin'];

        return $this->renderAjax('purchase-coin-prompt', [
            'coin' => $coin,
            'model' => $amountModel
        ]); 
    }
}
