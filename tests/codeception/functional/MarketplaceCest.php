<?php

/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Ghanmi Mortadha <mortadha.ghanmi56@gmail.com >
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace user\functional;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\permissions\ReviewPublicOffers;
use xcoin\FunctionalTester;

class MarketplaceCest
{
    // TODO: buy a product
    // TODO: change all click action with it's respective ajax get call
    // TODO: use createUrl method to create urls instead of hard-coding it

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
        $productOfferType = Product::OFFER_DISCOUNT_FOR_COINS;
        $productDiscount = 20;

        // New space with id = 5
        $I->createSpace($spaceToCreate, 'XcoinSpaceDescription', '#b5810a');
        $spaceToCreateId = 5;

        $asset = Asset::findOne(['space_id' => $spaceToCreateId]);

        $I->enableUserModule($userId, 'xcoin');
        $I->amOnUserProducts($userId);
        $I->see('Currently there are no products.');

        // We create a new product with discount offer type: 1
        $I->sendAjaxGetRequest('index.php?r=xcoin/marketplace/sell');
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
        $productOfferType = Product::OFFER_TOTAL_PRICE_IN_COINS;
        $productPrice = 20;
        $productPaymentType = Product::PAYMENT_PER_UNIT;
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
            'product_type' => Product::TYPE_PERSONAL,
            'price' => $productPrice,
            'payment_type' => $productPaymentType,
            'asset_id' => $asset->id
        ]);

        $productId = 3;

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
        $productOfferType = Product::OFFER_DISCOUNT_FOR_COINS;
        $productDiscount = 20;

        // New space with id = 5
        $I->createSpace($spaceToCreate, 'XcoinSpaceDescription', '#b5810a');
        $spaceToCreateId = 5;

        $asset = Asset::findOne(['space_id' => $spaceToCreateId]);

        $I->amOnSpaceProducts($spaceToCreateId);
        $I->see('Currently there are no products.');

        $space = Space::findOne(['id' => $spaceToCreateId]);

        $I->sendAjaxGetRequest('index.php?r=xcoin/product/create&cguid='.$space->guid);
        $I->sendAjaxPostRequest('index.php?r=xcoin/product/create&cguid='.$space->guid, [
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
            'product_type' => Product::TYPE_SPACE,
            'discount' => $productDiscount,
            'asset_id' => $asset->id,
            'space_id' => $spaceToCreateId
        ]);

        $productId = 3;

        $I->amOnMarketplace();
        $I->see($productName);

        $I->amOnProduct($productId);
        $I->see($productName);

        $I->amOnProductSpace($productId);
        $I->see($productName);

    }

    public function testUserProductReview(FunctionalTester $I)
    {
        $I->wantTo('ensure that user product review works');

        $userProductOwnerId = 3;

        $I->amUser1();

        $I->setGroupPermission(2, ReviewPublicOffers::class);

        $product = Product::findOne(['id' => 1]);

        $I->amOnMarketplace();
        $I->see($product->name);

        // Enabling xcoin module for the user before accessing his product/products page
        $I->enableUserModule($userProductOwnerId, 'xcoin');
        $I->amOnProduct($product->id);
        $I->see($product->name);

        $I->click(['class' => 'review-btn-trusted']);

        $I->amOnMarketplace(['verified' => Product::PRODUCT_REVIEWED]);
        $I->see($product->name);
    }

    public function testSpaceProductReview(FunctionalTester $I)
    {
        $I->wantTo('ensure that space product review works');

        $spaceProductOwnerId = 1;

        $I->amUser1();

        $I->setGroupPermission(2, ReviewPublicOffers::class);

        $product = Product::findOne(['id' => 2]);

        $I->amOnMarketplace();
        $I->see($product->name);

        // Enabling xcoin module for the space before accessing its product/products page
        $I->enableSpaceModule($spaceProductOwnerId, 'xcoin');
        $I->amOnProduct($product->id);
        $I->see($product->name);

        $I->click(['class' => 'review-btn-trusted']);

        $I->amOnMarketplace(['verified' => Product::PRODUCT_REVIEWED]);
        $I->see($product->name);
    }

    public function testUserProductEdit(FunctionalTester $I)
    {

        $I->wantTo('ensure that user product edit works');

        $I->amUser2();

        $owner = User::findOne(['id' => 3]);
        $product = Product::findOne(['id' => 1]); // this product is created by User2

        $I->enableUserModule($owner->id, 'xcoin');

        $I->sendAjaxGetRequest('index.php?r=xcoin/product/edit&id='.$product->id.'&cguid='.$owner->guid);
        $I->sendAjaxPostRequest('index.php?r=xcoin/product/edit&id='.$product->id.'&cguid='.$owner->guid, [
            'Product[name]' => $product->name.'_mod',
            'Product[description]' => $product->description.'_mod',
        ]);
        $I->seeRecord(Product::class, [
            'name' => $product->name.'_mod',
            'description' => $product->description.'_mod',
            'content' => $product->content,
            'offer_type' => $product->offer_type,
            'discount' => $product->discount,
            'asset_id' => $product->asset_id,
        ]);

    }

    public function testSpaceProductEditFailing(FunctionalTester $I)
    {

        $I->wantTo('ensure that space product edit fails because it can\'t manage assets');

        $I->amUser2();

        $owner = Space::findOne(['id' => 1]);
        $product = Product::findOne(['id' => 2]); // this product is created by Space 1

        $I->enableSpaceModule($owner->id, 'xcoin');

        $I->sendAjaxGetRequest('index.php?r=xcoin/product/edit&id='.$product->id.'&cguid='.$owner->guid);
        $I->sendAjaxPostRequest('index.php?r=xcoin/product/edit&id='.$product->id.'&cguid='.$owner->guid, [
            'Product[name]' => $product->name.'_mod',
            'Product[description]' => $product->description.'_mod',
        ]);
        $I->seeResponseCodeIs(401);

    }

    public function testUserProductDeletion(FunctionalTester $I)
    {

        $I->wantTo('ensure that user product deletion works');

        $I->amUser2();

        $owner = User::findOne(['id' => 3]);
        $product = Product::findOne(['id' => 1]); // this product is created by User2

        $I->enableUserModule($owner->id, 'xcoin');

        $I->sendAjaxGetRequest('index.php?r=xcoin/product/delete&id='.$product->id.'&cguid='.$owner->guid);
        $I->dontSeeRecord(Product::class, [
            'name' => $product->name,
            'description' => $product->description,
            'content' => $product->content,
            'offer_type' => $product->offer_type,
            'discount' => $product->discount,
            'asset_id' => $product->asset_id,
        ]);

    }

    public function testSpaceProductDeletionFailing(FunctionalTester $I)
    {

        $I->wantTo('ensure that space product deletion fails because it can\'t manage assets');

        $I->amUser2();

        $owner = Space::findOne(['id' => 1]);
        $product = Product::findOne(['id' => 2]); // this product is created by Space 1

        $I->enableSpaceModule($owner->id, 'xcoin');

        $I->sendAjaxGetRequest('index.php?r=xcoin/product/delete&id='.$product->id.'&cguid='.$owner->guid);
        $I->seeResponseCodeIs(401);

    }

}
