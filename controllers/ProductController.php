<?php

namespace humhub\modules\xcoin\controllers;

use humhub\components\Controller;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Product;
use Yii;

class ProductController extends Controller
{

    public function actionCreate()
    {
        $model = new Product();
        $model->scenario = Product::SCENARIO_CREATE;

        $assetList = [];
        foreach (Asset::find()->all() as $asset) {
            $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();
            return $this->htmlRedirect(['/xcoin/marketplace']);
        }

        return $this->renderAjax('create', ['model' => $model, 'assetList' => $assetList]);
    }

}
