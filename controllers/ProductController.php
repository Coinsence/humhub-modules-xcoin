<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Product;
use Yii;
use yii\web\HttpException;

class ProductController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public $validContentContainerClasses = [Space::class];

    public function actionIndex()
    {
        AssetHelper::initContentContainer($this->contentContainer);
        AccountHelper::initContentContainer($this->contentContainer);

        $products = Product::find()->where(['space_id' => $this->contentContainer->id])->all();

        return $this->render('index', [
            'products' => $products,
        ]);
    }

    public function actionCreate()
    {
        $model = new Product();
        $model->scenario = Product::SCENARIO_CREATE;
        $model->product_type = Product::TYPE_SPACE;
        $model->space_id = $this->contentContainer->id;

        $assetList = [];
        foreach (Asset::find()->all() as $asset) {
            $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->fileManager->attach(Yii::$app->request->post('fileList'));
            $this->view->saved();

            return $this->htmlRedirect(['/xcoin/product', 'container' => $this->contentContainer]);
        }

        return $this->renderAjax('create', ['model' => $model, 'assetList' => $assetList]);
    }

    public function actionOverview($productId)
    {
        $product = Product::findOne(['id' => $productId]);

        if(!$product) {
            throw new HttpException(404);
        }

        return $this->render('overview', [
            'product' => $product,
        ]);
    }
}
