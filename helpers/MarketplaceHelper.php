<?php

namespace humhub\modules\xcoin\helpers;

use humhub\modules\xcoin\models\Marketplace;
use Yii;

/**
 * MarketplaceHelper
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class MarketplaceHelper
{
    const CAROUSEL_MARKETPLACES_COUNT = 20;

    public static function getRandomMarketplaces()
    {
        $marketplacesCarousel = [];
        $randomMarketplaces = Marketplace::find()
            ->where(['status' => Marketplace::MARKETPLACE_STATUS_ENABLED, 'stopped' => Marketplace::MARKETPLACE_ACTIVE])
            ->asArray()
            ->all();

        if ($randomMarketplaces) {
            if (count($randomMarketplaces) > self::CAROUSEL_MARKETPLACES_COUNT) {
                foreach (array_rand($randomMarketplaces, self::CAROUSEL_MARKETPLACES_COUNT) as $marketplace) {

                    $marketplacesCarousel[] = [
                        'id' => $randomMarketplaces[$marketplace]['id'],
                        'text' => $randomMarketplaces[$marketplace]['title'],
                        'img' => self::getMarketplaceCoverUrl($randomMarketplaces[$marketplace]['id'])
                    ];
                }
            } else {
                shuffle($randomMarketplaces);
                foreach ($randomMarketplaces as $marketplace)
                    $marketplacesCarousel[] = [
                        'id' => $marketplace['id'],
                        'text' => $marketplace['title'],
                        'img' => self::getMarketplaceCoverUrl($marketplace['id'])
                    ];
            }
        }

        return $marketplacesCarousel;
    }

    public static function getMarketplaceCoverUrl($marketplaceId)
    {
        $marketplace = Marketplace::findOne(['id' => $marketplaceId]);

        if ($marketplace->getCover()) {
            return $marketplace->getCover()->getUrl();
        } else {
            return Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-marketplace-cover.png';
        }
    }
}
