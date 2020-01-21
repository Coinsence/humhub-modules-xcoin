<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace user\functional;

use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Product;
use xcoin\FunctionalTester;

class MarketplaceCest
{
    // TODO: verifying a product
    // TODO: buy a product

    public function testPageVisibility(FunctionalTester $I)
    {

        $I->wantTo('ensure that marketplace page works');
        $I->amOnMarketplace();
        $I->see('Marketplace');

    }

    public function testProductCreationByUser(FunctionalTester $I)
    {

        $I->amUser1();
        $userId = 2;

        $I->wantTo('ensure that product creation by user works');

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

        $I->amOnUserProducts($userId);
        $I->see('Currently there are no products.');

        // We create a new product with discount offer type: 1
        $I->sendAjaxPostRequest('index.php?r=xcoin/marketplace/sell', []);
        $I->sendAjaxPostRequest('index.php?r=xcoin/marketplace/sell', [
            'Product[name]' => $productName,
            'Product[description]' => $productDescription,
            'Product[content]' => $productFullDescription,
            'Product[offer_type]' => $productOfferType,
            'Product[asset_id]' => $asset->id,
            'Product[discount]' => $productDiscount,
        ]);
        $I->seeRecord(Product::class, [
            'name' => $productName,
            'description' => $productDescription,
            'content' => $productFullDescription,
            'offer_type' => $productOfferType,
            'product_type' => 1,
            'discount' => $productDiscount,
            'asset_id' => $asset->id
        ]);

        // We create a new product with total price in coins offer type: 2
        $productOfferType = 2;
        $productPrice = 20;
        $productPaymentType = 1;
        $I->sendAjaxPostRequest('index.php?r=xcoin/marketplace/sell', [
            'Product[name]' => $productName,
            'Product[description]' => $productDescription,
            'Product[content]' => $productFullDescription,
            'Product[offer_type]' => $productOfferType,
            'Product[asset_id]' => $asset->id,
            'Product[price]' => $productPrice,
            'Product[payment_type]' => $productPaymentType,
        ]);
        $I->seeRecord(Product::class, [
            'name' => $productName,
            'description' => $productDescription,
            'content' => $productFullDescription,
            'offer_type' => $productOfferType,
            'product_type' => 1,
            'price' => $productPrice,
            'payment_type' => $productPaymentType,
            'asset_id' => $asset->id
        ]);

        $productId = 1;

        $I->amOnMarketplace();
        $I->see($productName);

        $I->amOnProduct($productId);
        $I->see($productName);

        $I->amOnProductUser($productId);
        $I->see($productName);

    }

    public function testProductCreationBySpace(FunctionalTester $I)
    {

        $I->amUser1();

        $I->wantTo('ensure that product creation by space works');

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

        $I->amOnSpaceProducts($spaceToCreateId);
        $I->see('Currently there are no products.');

        $space = Space::findOne(['id' => $spaceToCreateId]);

        $I->sendAjaxPostRequest('index.php?r=xcoin/product/create&cguid='.$space->guid, []);
        $I->sendAjaxPostRequest('index.php?r=xcoin/product/create&cguid='.$space->guid, [
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
            'offer_type' => $productOfferType,
            'product_type' => 2,
            'discount' => $productDiscount,
            'asset_id' => $asset->id,
            'space_id' => $spaceToCreateId
        ]);

        $productId = 1;

        $I->amOnMarketplace();
        $I->see($productName);

        $I->amOnProduct($productId);
        $I->see($productName);

        $I->amOnProductSpace($productId);
        $I->see($productName);

    }

}