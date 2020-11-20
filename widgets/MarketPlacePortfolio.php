<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\xcoin\widgets;

use humhub\modules\user\models\User;
use Yii;

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
use yii\db\Expression;
use yii\web\HttpException;
/**
 * Displays the profile header of a user
 *
 * @since 0.5
 * @author Luke
 */
class MarketPlacePortfolio extends \yii\base\Widget
{

    /**
     * @var User
     */
    public $user;
    public $products;

    /**
     * @var boolean is owner of the current profile
     */
    protected $isProfileOwner = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        
        /**
         * Try to autodetect current user by controller
         */
        // if ($this->user === null) {
        //     $this->user = $this->getController()->getUser();
        // }

        // if (!Yii::$app->user->isGuest && Yii::$app->user->id == $this->user->id) {
        //     $this->isProfileOwner = true;
        // }
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        // test
        $query = Product::find();
        $query->andWhere(['xcoin_product.status' => Product::STATUS_AVAILABLE]);
        $query->andWhere(['IS NOT', 'xcoin_product.id', new Expression('NULL')]);
        $query->orderBy(['created_at' => SORT_DESC]);
        $query->andWhere(['review_status' => Product::PRODUCT_REVIEWED]);
        $query->innerJoin('xcoin_marketplace', 'xcoin_product.marketplace_id = xcoin_marketplace.id');
        $query->andWhere('xcoin_marketplace.status = 1');
        $query->andWhere('xcoin_marketplace.stopped = 0');

        $model = new ProductFilter();
        $marketplaceId = null;
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

        $marketplace = Marketplace::findOne(['id' => $marketplaceId]);
        // test
        //  
        if (!Yii::$app->user->isGuest && Yii::$app->useR->id == $this->user->id) {
            $this->isProfileOwner = true;
        }
        // test
        return $this->render('marketPlacePortfolio', [
            'selectedMarketplace' => $marketplace,
            'model' => $model,
            'assetsList' => $assetsList,
            'countriesList' => $countriesList,
            'user' => $this->user,
        'isProfileOwner' => $this->isProfileOwner,
            'products' => $query->all(),
            'marketplacesCarousel' => MarketplaceHelper::getRandomMarketplaces()
        ]);
        
    }
}

?>
























