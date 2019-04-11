<?php

namespace humhub\modules\xcoin\controllers;

use humhub\components\Controller;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Product;
use Yii;

class MarketplaceController extends Controller
{

    public function actionIndex()
    {
        $products = Product::find()->where(['status' => Product::STATUS_AVAILABLE ])->all();

        return $this->render('index', [
            'products' => $products,
        ]);
    }

    public function actionSell()
    {
        $model = new Product();
        $model->scenario = Product::SCENARIO_CREATE;
        $model->product_type = Product::TYPE_PERSONAL;

        $assetList = [];
        foreach (Asset::find()->all() as $asset) {
            $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->fileManager->attach(Yii::$app->request->post('fileList'));
            $this->view->saved();

            return $this->htmlRedirect(['/xcoin/marketplace']);
        }

        return $this->renderAjax('sell', ['model' => $model, 'assetList' => $assetList]);
    }
}
