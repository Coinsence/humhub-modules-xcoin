<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace user\functional;

use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Product;
use xcoin\FunctionalTester;

class MarketplaceCest
{
    public function testPageVisibility(FunctionalTester $I)
    {

        $I->wantTo('ensure that marketplace page works');
        $I->amOnMarketplace();
        $I->see('Marketplace');

    }

    public function testProductCreation(FunctionalTester $I)
    {

        $I->amUser1();

        $I->wantTo('ensure that product creation works');

        $spaceToCreate = 'XcoinSpace 1';

        $productName = 'Testing Product';
        $productDescription = 'ProductDescription';
        $productFullDescription = 'ProductFullDescription';
        $productOfferType = 1;
        $productDiscount = 20;

        // New space with id = 5
        $I->createSpace($spaceToCreate, 'XcoinSpaceDescription', '#b5810a');
        $spaceToCreateId = 5;

        $asset = Asset::findOne(['space_id' => $spaceToCreateId]);

        $I->sendAjaxPostRequest('index.php?r=xcoin/marketplace/sell', [
            'Product[name]' => $productName,
            'Product[description]' => $productDescription,
            'Product[content]' => $productFullDescription,
            'Product[offer_type]' => $productOfferType,
            'Product[asset_id]' => $asset->id,
            'Product[discount]' => $productDiscount,
            // 'Product[price]' => $projectTitle,
            // 'Product[payment_type]' => $projectDescription,
        ]);

        $I->seeRecord(Product::class, [
            'name' => $productName,
            'description' => $productDescription,
            'content' => $productFullDescription,
            'product_type' => $productOfferType,
            'discount' => $productDiscount,
            'asset_id' => $asset->id
        ]);

    }

}