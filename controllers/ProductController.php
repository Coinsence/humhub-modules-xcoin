<?php

namespace humhub\modules\xcoin\controllers;

use Exception;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\Marketplace;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\widgets\MarketplaceImage;
use Throwable;
use Yii;
use yii\web\HttpException;

class ProductController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public function actionIndex()
    {
        if ($this->contentContainer instanceof Space) {
            $products = Product::find()->where(['space_id' => $this->contentContainer->id])->all();

            return $this->render('index', [
                'products' => $products,
            ]);
        } else {
            $products = Product::find()->where([
                'created_by' => $this->contentContainer->id,
                'product_type' => Product::TYPE_PERSONAL
            ])->all();

            return $this->render('index_user', [
                'products' => $products,
            ]);
        }
    }

    public function actionNew($marketplaceId)
    {
        if (!$marketplace = Marketplace::findOne(['id' => $marketplaceId])) {
            throw new HttpException(404, 'Marketplace not found!');
        }

        if ($marketplace->isStopped()) {
            throw new HttpException(403, 'You can`t sell a product in a closed marketplace!');
        }

        $user = Yii::$app->user->identity;

        $model = new Product();
        $model->created_by = $user->id;
        $model->marketplace_id = $marketplace->id;
        $model->scenario = Product::SCENARIO_CREATE;

        if (empty(Yii::$app->request->post('step'))) {

            $spaces = SpaceHelper::getSellerSpaces($user);

            $spacesList = [];
            foreach ($spaces as $space) {
                if (AssetHelper::getSpaceAsset($space))
                    $spacesList[$space->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
            }

            return $this->renderAjax('../product/spaces-list', [
                'product' => $model,
                'spacesList' => $spacesList,
            ]);
        }

        if (Yii::$app->request->post('personal-product') == '1') {
            $model->space_id = null;
            $model->product_type = Product::TYPE_PERSONAL;
        } else {
            $model->product_type = Product::TYPE_SPACE;
        }

        $model->load(Yii::$app->request->post());

        // Try Save Step 2
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '2') {

            if ($model->marketplace->isStopped()) {
                throw new HttpException(403, 'You can`t sell a product in a closed marketplace!');
            }

            if ($model->space && !SpaceHelper::canSellProduct($model->space)) {
                throw new HttpException(401);
            }

            // Step 3: Gallery
            return $this->renderAjax('../product/media', ['model' => $model]);
        }

        // Try Save Step 3
        if (
            Yii::$app->request->isPost &&
            Yii::$app->request->post('step') == '3'
            && $model->isNameUnique()
            && $model->save()
        ) {
            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();


            $url = $model->isSpaceProduct() ?
                $model->space->createUrl('/xcoin/product/overview', [
                    'container' => $model->space,
                    'productId' => $model->id
                ]) :
                $user->createUrl('/xcoin/product/overview', [
                    'container' => $user,
                    'productId' => $model->id
                ]);

            return $this->redirect($url);
        }

        // Check validation
        if ($model->hasErrors() && $model->isSecondStep()) {

            return $this->renderAjax('../product/details', [
                'model' => $model,
                'myAsset' => $model->space ? AssetHelper::getSpaceAsset($model->space) : null
            ]);
        }

        // Step 2: Details
        return $this->renderAjax('../product/details', [
            'model' => $model,
            'myAsset' => $model->space ? AssetHelper::getSpaceAsset($model->space) : null
        ]);
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
            if ($asset->getIssuedAmount()) {
                $assetList[$asset->id] = SpaceImage::widget(['space' => $asset->space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $asset->space->name;
            }
        }

        if (Yii::$app->request->isPost && $model->save()) {

            $model->fileManager->attach(Yii::$app->request->post('fileList'));
            $this->view->saved();

            return $this->htmlRedirect(['/xcoin/product', 'container' => $this->contentContainer]);
        }

        return $this->renderAjax('edit', ['model' => $model, 'assetList' => $assetList]);
    }

    /**
     * @throws Throwable
     */
    public function actionDelete()
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

        $model->delete();

        $this->view->saved();

        return $this->htmlRedirect(['/xcoin/product', 'container' => $this->contentContainer]);
    }

    public function actionReview($id, $status)
    {

        $model = Product::findOne(['id' => $id]);

        if (!SpaceHelper::canReviewProject($model->marketplace->space) && !PublicOffersHelper::canReviewSubmittedProjects()) {
            throw new HttpException(401);
        }

        $model->scenario = Product::SCENARIO_EDIT;
        $model->review_status = $status;

        $model->save();

        $this->view->saved();

        return $this->redirect($this->contentContainer->createUrl('/xcoin/product/overview', [
            'container' => $this->contentContainer,
            'productId' => $model->id
        ]));
    }
}
