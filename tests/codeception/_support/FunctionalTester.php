<?php

/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Ghanmi Mortadha <mortadha.ghanmi56@gmail.com >
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace xcoin;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\Product;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \FunctionalTester
{
    use _generated\FunctionalTesterActions;
    
   /**
    * Define custom actions here
    */

    public function amOnCrowdfunding($params = [])
    {
        return tests\codeception\_pages\CrowdfundingPage::openBy($this, $params);
    }

    public function amOnMarketplace($params = [])
    {
        return tests\codeception\_pages\MarketplacePage::openBy($this, $params);
    }


    public function createSpace($name, $description, $color)
    {
        $this->sendAjaxPostRequest('index.php?r=space/create/create', [
            'Space[color]' => $color,
            'Space[name]' => $name,
            'Space[description]' => $description,
            'Space[visibility]' => 1,
            'Space[join_policy]' => 2
        ]);
        $this->seeRecord(Space::class, [
            'name' => $name,
            'visibility' => 1,
            'join_policy' => 2,
        ]);
    }


    public function enableSpaceModule($spaceId = null, $moduleId)
    {
        if($spaceId == null) {
           return;
        }

        $space = Space::findOne(['id' => $spaceId]);
        $space->enableModule($moduleId);
        \Yii::$app->moduleManager->flushCache();
    }

    public function enableUserModule($userId = null, $moduleId)
    {
        if($userId == null) {
            return;
        }

        $user = User::findOne(['id' => $userId]);
        if (!$user->isModuleEnabled($moduleId)) {
            $user->enableModule($moduleId);
        }
        \Yii::$app->moduleManager->flushCache();
    }


    public function amOnSpaceProjects($spaceId = null)
    {

        if ($spaceId == null) {
            return;
        } else {
            $space = Space::findOne(['id' => $spaceId]);
            $url = $space->createUrl('/xcoin/funding/index');
            $this->amOnPage($url);
        }

    }

    public function amOnProject($projectId = null)
    {

        if ($projectId == null) {
            return;
        } else {
            $funding = Funding::findOne(['id' => $projectId]);
            $space = $funding->getSpace()->one();
            $url = $space->createUrl('/xcoin/funding/overview', [
                'fundingId' => $funding->id
            ]);
            $this->amOnPage($url);
        }

    }

    public function amOnProjectSpace($projectId = null)
    {

        if ($projectId == null) {
            return;
        } else {
            $funding = Funding::findOne(['id' => $projectId]);
            $space = $funding->getSpace()->one();
            $url = $space->createUrl('/xcoin/funding/index', [
                'fundingId' => $funding->id
            ]);
            $this->amOnPage($url);
        }

    }


    public function amOnUserProducts($userId = null)
    {

        if ($userId == null) {
            return;
        } else {
            $user = User::findOne(['id' => $userId]);
            $url = $user->createUrl('/xcoin/product/index');
            $this->amOnPage($url);
        }

    }

    public function amOnSpaceProducts($spaceId = null)
    {

        if ($spaceId == null) {
            return;
        } else {
            $space = Space::findOne(['id' => $spaceId]);
            $url = $space->createUrl('/xcoin/product/index');
            $this->amOnPage($url);
        }

    }

    public function amOnProduct($productId = null)
    {

        if ($productId == null) {
            return;
        } else {
            $product = Product::findOne(['id' => $productId]);
            $user = $product->getCreatedBy()->one();
            $space = $product->getSpace()->one();
            $url = $product->isSpaceProduct() ? $space->createUrl('/xcoin/product/overview', [
                'productId' => $product->id
            ]) : $user->createUrl('/xcoin/product/overview', [
                'productId' => $product->id
            ]);
            $this->amOnPage($url);
        }

    }

    public function amOnProductUser($productId = null)
    {

        if ($productId == null) {
            return;
        } else {
            $product = Product::findOne(['id' => $productId]);
            $user = $product->getCreatedBy()->one();
            $url = $user->createUrl('/xcoin/product/index');
            $this->amOnPage($url);
        }

    }

    public function amOnProductSpace($productId = null)
    {

        if ($productId == null) {
            return;
        } else {
            $product = Product::findOne(['id' => $productId]);
            $space = $product->getSpace()->one();
            $url = $space->createUrl('/xcoin/product/index');
            $this->amOnPage($url);
        }

    }


    public function amOnSpaceAccountsOverview($spaceId = null)
    {

        if ($spaceId === null) {
            return;
        } else {
            $space = Space::findOne(['id' => $spaceId]);
            $url = $space->createUrl('/xcoin/overview/index');
            $this->amOnPage($url);
        }

    }

    public function amOnUserAccountsOverview($userId = null)
    {

        if ($userId === null) {
            return;
        } else {
            $user = User::findOne(['id' => $userId]);
            $url = $user->createUrl('/xcoin/overview/index');
            $this->amOnPage($url);
        }

    }

    public function amOnSpaceDefaultAccountDetails($spaceId = null)
    {

        if ($spaceId == null) {
            return;
        } else {
            $account = Account::findOne(['space_id' => $spaceId, 'account_type' => Account::TYPE_DEFAULT]);
            $space = $account->getSpace()->one();
            $url = $space->createUrl('/xcoin/account', ['id' => $account->id]);
            $this->amOnPage($url);

        }

    }

    public function amOnUserDefaultAccountDetails($userId = null)
    {

        if ($userId == null) {
            return;
        } else {
            $account = Account::findOne(['user_id' => $userId, 'account_type' => Account::TYPE_DEFAULT]);
            $user = $account->getUser()->one();
            $url = $user->createUrl('/xcoin/account', ['id' => $account->id]);
            $this->amOnPage($url);

        }

    }

    public function amOnSpaceAccountDetails($accountId = null)
    {

        if ($accountId == null) {
            return;
        } else {
            $account = Account::findOne(['id' => $accountId]);
            $space = $account->getSpace()->one();
            $url = $space->createUrl('/xcoin/account', ['id' => $account->id]);
            $this->amOnPage($url);

        }

    }

    public function amOnUserAccountDetails($accountId = null)
    {

        if ($accountId == null) {
            return;
        } else {
            $account = Account::findOne(['id' => $accountId]);
            $user = $account->getUser()->one();
            $url = $user->createUrl('/xcoin/account', ['id' => $account->id]);
            $this->amOnPage($url);

        }

    }

}
