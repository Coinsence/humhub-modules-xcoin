<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\MarketplaceHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Challenge;
use humhub\components\Controller;
use humhub\modules\xcoin\models\Marketplace;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\models\ProductFilter;
use humhub\modules\xcoin\widgets\MarketplaceImage;
use Yii;
use yii\db\Expression;
use yii\web\HttpException;

class MarketplaceOverviewController extends Controller
{
    
    
    public function actionIndex($marketplaceId = null)
    {
        $query = Product::find();
        $query->andWhere(['xcoin_product.status' => Product::STATUS_AVAILABLE]);
        $query->andWhere(['IS NOT', 'xcoin_product.id', new Expression('NULL')]);
        $query->orderBy(['created_at' => SORT_DESC]);
        $query->andWhere(['review_status' => Product::PRODUCT_REVIEWED]);
        $query->innerJoin('xcoin_marketplace', 'xcoin_product.marketplace_id = xcoin_marketplace.id');
        $query->andWhere('xcoin_marketplace.status = 1');
        $query->andWhere('xcoin_marketplace.stopped = 0');

        $model = new ProductFilter();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->asset_id)
                $query->andWhere(['xcoin_marketplace.asset_id' => $model->asset_id]);
            if ($model->categories) {
                $query->joinWith('categories category');
                $query->andWhere(['category.id' => $model->categories]);
            }

            if ($model->country)
                $query->andWhere(['country' => $model->country]);
            if ($model->city)
                $query->andWhere(['like', 'city', $model->city . '%', false]);
            if ($model->keywords)
                $query->andWhere(['like', 'xcoin_product.name', '%' . $model->keywords . '%', false]);

        } else if ($marketplaceId) {
            $query->andWhere(['marketplace_id' => $marketplaceId]);
        }

        if ($marketplaceId) {
            $marketplacesList = Marketplace::findAll(['id' => $marketplaceId]);
        } else {
            $marketplacesList = Marketplace::findAll(['status' => Marketplace::MARKETPLACE_STATUS_ENABLED, 'stopped' => Marketplace::MARKETPLACE_ACTIVE]);
        }

        $assetsList = [];
        $countriesList = [];

        foreach ($marketplacesList as $marketplace) {
            $asset = $marketplace->asset;
            $space = $marketplace->asset->space;
            $assetsList[$asset->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
        }
        $user = Yii::$app->user->identity;

        $marketplace = Marketplace::findOne(['id' => $marketplaceId]);

        return $this->render('index', [
            'selectedMarketplace' => $marketplace,
            'model' => $model,
            'assetsList' => $assetsList,
            'countriesList' => $countriesList,
            'products' => $query->all(),
            'marketplacesCarousel' => MarketplaceHelper::getRandomMarketplaces(),
            'user'=>$user
        ]);
    }


    public function actionNew()
    {
        /** @var Challenge[] $challenges */
        $marketplaces = Marketplace::find()->all();
        if (empty($marketplaces)) {
            $this->view->info(Yii::t('XcoinModule.marketplace', 'In order to sell a product, there must be open marketplaces.'));

            return $this->htmlRedirect('/xcoin/marketplace-overview');
        }

        $user = Yii::$app->user->identity;

        $model = new Product();
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

        $model->load(Yii::$app->request->post());

        // Step 1: Choose marketplace
        if ($model->isFirstStep()) {
            if (Yii::$app->request->post('personal-product') == '1') {
                $model->space_id = null;
                $model->product_type = Product::TYPE_PERSONAL;
            } else {
                $model->product_type = Product::TYPE_SPACE;
            }

            $marketplacesList = [];

            foreach ($marketplaces as $marketplace) {
                if ($marketplace->isStopped() or $marketplace->isDisabled())
                    continue;

                if ($model->space) {
                    if (AssetHelper::getSpaceAsset($model->space)->id != $marketplace->asset_id)
                        $marketplacesList[$marketplace->id] = MarketplaceImage::widget(['marketplace' => $marketplace, 'width' => 16, 'link' => true]);
                } else {
                    $marketplacesList[$marketplace->id] = MarketplaceImage::widget(['marketplace' => $marketplace, 'width' => 16, 'link' => true]);
                }
            }

            return $this->renderAjax('../product/create', [
                    'model' => $model,
                    'marketplacesList' => $marketplacesList
                ]
            );
        }

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
}
