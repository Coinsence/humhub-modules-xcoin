<?php

namespace humhub\modules\xcoin\controllers;

use Exception;
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
    public function actionIndex()
    {
        AssetHelper::initContentContainer($this->contentContainer);
        AccountHelper::initContentContainer($this->contentContainer);

        if ($this->contentContainer instanceof Space) {
            $products = Product::find()->where(['space_id' => $this->contentContainer->id])->all();

            return $this->render('index', [
                'products' => $products,
            ]);
        } else {
            $products = Product::find()->where([
                'created_by' => Yii::$app->user->id,
                'product_type' => Product::TYPE_PERSONAL
            ])->all();

            return $this->render('index_user', [
                'products' => $products,
            ]);
        }
    }

    /**
     * @return string
     * @throws Exception
     */
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

    /**
     * @param $productId
     * @return string
     * @throws HttpException
     */
    public function actionOverview($productId)
    {
        $product = Product::findOne(['id' => $productId]);

        if (!$product) {
            throw new HttpException(404);
        }

        return $this->render('overview', [
            'product' => $product,
        ]);
    }

    /**
     * @throws HttpException
     * @throws Exception
     */
    public function actionEdit()
    {
        if ($this->contentContainer instanceof Space) {
            if (!AssetHelper::canManageAssets($this->contentContainer)) {
                throw new HttpException(401);
            }

            $model = Product::findOne(['id' => Yii::$app->request->get('id'), 'space_id' => $this->contentContainer->id]);
        } else {
            $model = Product::findOne(['id' => Yii::$app->request->get('id')]);
            if ($model && !$model->isOwner(Yii::$app->user->identity)) {
                throw new HttpException(401);
            }
        }

        if ($model === null) {
            throw new HttpException(404);
        }


        $model->scenario = Product::SCENARIO_EDIT;
        $model->load(Yii::$app->request->post());

        $assetList = [];
        foreach (Asset::find()->all() as $asset) {
            $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
        }

        if (Yii::$app->request->isPost && $model->save()) {

            $model->fileManager->attach(Yii::$app->request->post('fileList'));
            $this->view->saved();

            return $this->htmlRedirect(['/xcoin/product', 'container' => $this->contentContainer]);
        }

        return $this->renderAjax('edit', ['model' => $model, 'assetList' => $assetList]);
    }
}
