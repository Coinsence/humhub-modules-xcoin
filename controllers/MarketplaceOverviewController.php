<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\file\models\File;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\MarketplaceHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\components\Controller;
use humhub\modules\xcoin\models\Marketplace;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\models\ProductFilter;
use humhub\modules\xcoin\utils\ImageUtils;
use humhub\modules\xcoin\widgets\MarketplaceImage;
use Yii;
use yii\db\Expression;
use yii\web\HttpException;

class MarketplaceOverviewController extends Controller
{

    public function getAccessRules()
    {
        return [
            ['login']
        ];
    }

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
            'user' => $user
        ]);
    }


    public function actionNew()
    {
        /** @var Marketplace[] $marketplaces */
        $marketplaces = Marketplace::find()->all();
        if (empty($marketplaces)) {
            $this->view->info(Yii::t('XcoinModule.marketplace', 'In order to sell a product, there must be open marketplaces.'));

            return $this->htmlRedirect('/xcoin/marketplace-overview');
        }

        $user = Yii::$app->user->identity;

        $model = new Product();
        $model->scenario = Product::SCENARIO_CREATE;

        $model->load(Yii::$app->request->post());

        // Step 1: Choose marketplace
        if ($model->isFirstStep()) {

            $marketplacesList = $products = [];

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

            foreach (Product::findAll(['created_by' => $user->id]) as $product) {
                $products[$product->id] = $product->name;
            }

            return $this->renderAjax('../product/create', [
                    'model' => $model,
                    'marketplacesList' => $marketplacesList,
                    'products' => $products
                ]
            );
        }


        // Step 2: Details

        $spaces = SpaceHelper::getSellerSpaces($user);

        $accountsList = [];

        $accountsList[Product::PRODUCT_USER_DEFAULT_ACCOUNT] = UserImage::widget(['user' => $user, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' Default';

        foreach ($spaces as $space) {
            if (AssetHelper::getSpaceAsset($space))
                $accountsList[$space->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
        }

        if ($model->isSecondStep()) {

            $model->account = Product::PRODUCT_USER_DEFAULT_ACCOUNT;
        }

        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '1') {

            if (!empty($model->clone_id) && null !== $clone = Product::findOne(['id' => $model->clone_id, 'created_by' => $user->id])) {
                $model->cloneProduct($clone);
            }

            return $this->renderAjax('../product/details', [
                'model' => $model,
                'accountsList' => $accountsList,
                'imageError' => null
            ]);
        }

        // Step 3: Gallery
        if (Yii::$app->request->isPost && Yii::$app->request->post('step') == '2') {
            if ($model->marketplace->isStopped()) {
                throw new HttpException(403, 'You can`t sell a product in a closed marketplace!');
            }

            if ($model->space && !SpaceHelper::canSellProduct($model->space)) {
                throw new HttpException(401);
            }

            if ($model->account == Product::PRODUCT_USER_DEFAULT_ACCOUNT) {
                $model->space_id = null;
                $model->product_type = Product::TYPE_PERSONAL;
            } else {
                $model->space_id = $model->account;
                $model->product_type = Product::TYPE_SPACE;
            }

            return $this->renderAjax('../product/media', ['model' => $model]);
        }

        // Try Saving
        if (
            Yii::$app->request->isPost &&
            Yii::$app->request->post('step') == '3' &&
            $model->isNameUnique() &&
            $model->validate()
        ) {
            $imageValidation = ImageUtils::checkImageSize(Yii::$app->request->post('fileList'));
            if ($imageValidation == false) {

                return $this->renderAjax('../product/details', [
                        'model' => $model,
                        'accountsList' => $accountsList,
                        'imageError' => "Image size cannot be more than 500 kb"
                    ]
                );
            }

            $model->save();

            if (null !== Yii::$app->request->post('fileList')) {
                $model->fileManager->attach(Yii::$app->request->post('fileList'));

            } elseif (!empty($model->clone_id) && null !== $file = File::findOne(['guid' => $model->picture_file_guid])) {
                $file->object_id = $model->id;
                $file->save();
            }

            $this->view->saved();

            return $this->renderAjax('../product/product-overview', [
                'model' => $model,
                'id' => $model->id
            ]);
        }

        // Check validation
        if ($model->hasErrors() && $model->isSecondStep()) {

            return $this->renderAjax('../product/details', [
                'model' => $model,
                'accountsList' => $accountsList,
                'imageError' => null
            ]);

        }

        if (Yii::$app->request->isPost && Yii::$app->request->post('overview') == '1') {
            $url = $model->isSpaceProduct() ?
                $model->space->createUrl('/xcoin/product/overview', [
                    'container' => $model->space,
                    'productId' => Yii::$app->request->post('prodId')
                ]) :
                $user->createUrl('/xcoin/product/overview', [
                    'container' => $user,
                    'productId' => Yii::$app->request->post('prodId')
                ]);

            return $this->redirect($url);
        }

    }
}
