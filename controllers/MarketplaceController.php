<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Marketplace;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\utils\ImageUtils;
use Yii;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Description of MarketplaceController
 */
class MarketplaceController extends ContentContainerController
{
    /**
     * @inheritdoc
     */
    public $validContentContainerClasses = [Space::class];

    public function actionIndex()
    {
        if (Yii::$app->user->isAdmin()) {
            $marketplaces = Marketplace::find()
                ->where(['space_id' => $this->contentContainer->id])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
        } else {
            $marketplaces = Marketplace::find()
                ->where(['space_id' => $this->contentContainer->id])
                ->andWhere(['hidden' => Marketplace::MARKETPLACE_SHOWN])
                ->orderBy(['created_at' => SORT_DESC])
                ->all();
        }

        return $this->render('index', [
            'marketplaces' => $marketplaces,
            'user' => $this->contentContainer
        ]);
    }

    /**
     * @param $marketplaceId
     * @return string
     * @throws HttpException
     */
    public function actionOverview($marketplaceId, $categoryId = null)
    {
        $marketplace = Marketplace::findOne(['id' => $marketplaceId, 'space_id' => $this->contentContainer]);

        if (!$marketplace) {
            throw new HttpException(404);
        }

        if (Space::findOne(['id' => $marketplace->space_id])->isAdmin(Yii::$app->user->identity)) {
            $products = $marketplace->getProducts()->all();
        } else {
            if ($marketplace->showUnreviewedSubmissions()) {
                $products = Product::findAll([
                    'marketplace_id' => $marketplace->id,
                    'status' => Product::STATUS_AVAILABLE,
                ]);
            } else {
                $products = Product::findAll([
                    'marketplace_id' => $marketplace->id,
                    'review_status' => Product::PRODUCT_REVIEWED,
                    'status' => Product::STATUS_AVAILABLE,
                ]);
            }
        }

        $categories = [];
        foreach ($products as $product) {
            foreach ($product->getCategories()->all() as $category) {
                $categories[] = $category;
            }
        }

        if ($categoryId) {
            $products = array_filter($products, function ($product) use ($categoryId) {
                $categories_ids = array_map(function ($category) {
                    return $category->id;
                }, $product->getCategories()->all());

                return in_array($categoryId, $categories_ids);
            });
        }

        return $this->render('overview', [
            'marketplace' => $marketplace,
            'products' => $products,
            'categories' => array_unique($categories, SORT_REGULAR),
            'activeCategory' => $categoryId,
        ]);
    }

    /**
     * @return string|Response
     * @throws HttpException
     */
    public function actionCreate()
    {
        /** @var Space $currentSpace */
        $currentSpace = $this->contentContainer;

        if (!AssetHelper::canManageAssets($currentSpace)) {
            throw new HttpException(401);
        }

        $model = new Marketplace();
        $model->scenario = Marketplace::SCENARIO_CREATE;
        $model->space_id = $this->contentContainer->id;

        $assets = AssetHelper::getAllAssets();
        $defaultAsset = AssetHelper::getDefaultAsset();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $imageValidation = ImageUtils::checkImageSize(Yii::$app->request->post('fileList'));
            if ($imageValidation == false) {
                return $this->renderAjax('create', [
                        'model' => $model,
                        'assets' => $assets,
                        'defaultAsset' => $defaultAsset,
                        'imageError' => "Image size cannot be more than 500 kb"
                    ]
                );
            }
            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();

            return $this->htmlRedirect($currentSpace->createUrl('/xcoin/marketplace/index', [
                'marketplaceId' => $model->id
            ]));
        }

        return $this->renderAjax('create', [
                'model' => $model,
                'assets' => $assets,
                'defaultAsset' => $defaultAsset,
                'imageError' => null

            ]
        );
    }

    /**
     * @return string|Response
     * @throws HttpException
     */
    public function actionEdit()
    {
        /** @var Space $currentSpace */
        $currentSpace = $this->contentContainer;

        if (!AssetHelper::canManageAssets($currentSpace)) {
            throw new HttpException(401);
        }

        $model = Marketplace::findOne(['id' => Yii::$app->request->get('id')]);

        if ($model == null) {
            throw new HttpException(404, Yii::t('AdminModule.controllers_MarketplaceController', 'Marketplace not found!'));
        }

        $model->scenario = Marketplace::SCENARIO_EDIT;

        $assets = AssetHelper::getAllAssets();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $imageValidation = ImageUtils::checkImageSize(Yii::$app->request->post('fileList'));
            if ($imageValidation == false) {
                return $this->renderAjax('edit', [
                        'model' => $model,
                        'assets' => $assets,
                        'imageError' => "Image size cannot be more than 500 kb"
                    ]
                );
            }
            $model->fileManager->attach(Yii::$app->request->post('fileList'));

            $this->view->saved();

            return $this->htmlRedirect($currentSpace->createUrl('/xcoin/marketplace/overview', [
                'marketplaceId' => $model->id
            ]));
        }

        return $this->renderAjax('edit', [
                'model' => $model,
                'assets' => $assets,
                'imageError' => null
            ]
        );
    }

    public function actionReviewProduct($id, $status)
    {
        $model = Product::findOne(['id' => $id]);
        if ($model == null) {
            throw new HttpException(404, 'Product Not found');
        }

        if (!SpaceHelper::canReviewProject($model->marketplace->space) && !PublicOffersHelper::canReviewSubmittedProjects()) {
            throw new HttpException(401);
        }

        $model->scenario = Product::SCENARIO_REVIEW;
        $model->review_status = $status;

        $model->save();

        $this->view->saved();

        return $this->redirect($this->contentContainer->createUrl('/xcoin/marketplace/overview', [
            'container' => $this->contentContainer,
            'marketplaceId' => $model->marketplace->id
        ]));
    }
}
