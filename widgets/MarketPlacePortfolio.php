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
use humhub\modules\space\models\Space;

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
        if ($this->user instanceof Space) {
            $products = Product::find()->where(['space_id' => $this->user->id])->all();

            return $this->render('marketPlacePortfolio', [
                'products' => $products,
            ]);
        } else {
            $products = Product::find()->where([
                'created_by' => $this->user->id,
                'product_type' => Product::TYPE_PERSONAL
            ])->all();

            return $this->render('marketPlacePortfolio', [
                'products' => $products,
            ]);
        }
    }

}

