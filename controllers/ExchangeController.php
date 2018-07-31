<?php

namespace humhub\modules\xcoin\controllers;

use Yii;
use humhub\components\Controller;
use humhub\modules\xcoin\models\Account;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Exchange;
use humhub\modules\xcoin\widgets\ExchangeFilter;
use humhub\modules\xcoin\models\ExchangeBuy;

class ExchangeController extends Controller
{

    public function actionIndex()
    {
        $query = Exchange::find();
        ExchangeFilter::applyFilters($query);

        return $this->render('index', ['query' => $query]);
    }

    public function actionOffer()
    {
        $fromAccount = Account::findOne(['id' => Yii::$app->request->get('accountId')]);
        if ($fromAccount === null) {
            return $this->renderAjax('@xcoin/views/transaction/select-account', ['contentContainer' => Yii::$app->user->getIdentity(), 'nextRoute' => ['/xcoin/exchange/offer']]);
        }

        $accountAssetList = [];
        foreach ($fromAccount->getAssets() as $asset) {
            $max = $fromAccount->getAssetBalance($asset);
            if (!empty($max)) {
                $accountAssetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16]) . ' ' . $asset->space->name .
                        '<small class="pull-rightx"> - max. ' . $max . '</small>';
            }
        }

        if (empty($accountAssetList)) {
            throw new HttpException(404, 'No assets available');
        }

        $assetList = [];
        foreach (Asset::find()->all() as $asset) {
            $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16]) . ' ' . $asset->space->name;
        }

        $exchange = new \humhub\modules\xcoin\models\Exchange();
        $exchange->scenario = Exchange::SCENARIO_OFFER;
        $exchange->minimum_amount = 0.01;
        $exchange->account_id = $fromAccount->id;

        if ($exchange->load(Yii::$app->request->post()) && $exchange->save()) {
            return $this->htmlRedirect(['/xcoin/exchange']);
        }

        return $this->renderAjax('offer', ['fromAccount' => $fromAccount, 'exchange' => $exchange, 'accountAssetList' => $accountAssetList, 'assetList' => $assetList]);
    }

    public function actionDelete($exchangeId)
    {
        $exchange = Exchange::findOne(['id' => $exchangeId]);
        if ($exchange === null) {
            throw new \yii\web\HttpException(404, 'Exchange not found!');
        }

        if ($exchange->created_by !== Yii::$app->user->id) {
            throw new \yii\web\HttpException(401, 'Access denied!');
        }

        $exchange->delete();

        return $this->redirect(['/xcoin/exchange']);
    }

    public function actionBuy($exchangeId)
    {
        $exchange = Exchange::findOne(['id' => $exchangeId]);
        if ($exchange === null) {
            throw new \yii\web\HttpException(404, 'Exchange not found!');
        }

        $fromAccount = Account::findOne(['id' => Yii::$app->request->get('accountId')]);
        if ($fromAccount === null) {
            return $this->renderAjax('@xcoin/views/transaction/select-account', [
                        'contentContainer' => Yii::$app->user->getIdentity(),
                        'nextRoute' => ['/xcoin/exchange/buy', 'exchangeId' => $exchange->id],
                        'disableAccount' => $exchange->account
            ]);
        }

        $model = new ExchangeBuy();
        $model->fromAccount = $fromAccount;
        $model->exchange = $exchange;
        $model->amountBuy = 1;

        if ($model->load(Yii::$app->request->post()) && $model->buy()) {
            return $this->htmlRedirect(['/xcoin/exchange']);
        }


        return $this->renderAjax('buy', [
                    'exchange' => $exchange,
                    'model' => $model,
                    'fromAccount' => $fromAccount
        ]);
    }

}
