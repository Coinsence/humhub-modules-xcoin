<?php

/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Ghanmi Mortadha <mortadha.ghanmi56@gmail.com >
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace xcoin\functional;

use humhub\modules\space\MemberEvent;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Transaction;
use xcoin\FunctionalTester;

class AccountingCest
{

    public function testSpaceDefaultAccount(FunctionalTester $I)
    {

        $I->wantTo('ensure that space default account works');

        $I->amUser1();

        $I->enableSpaceModule(2, 'xcoin');

        $I->amOnSpaceAccountsOverview(2);
        $I->see('Accounts of this space');

        $I->amOnSpaceDefaultAccountDetails(2);
        $I->see('Account overview');

    }

    public function testSpaceNoDirectCoinTransfer(FunctionalTester $I)
    {

        $I->wantTo('ensure that space direct coin transfer is disabled');

        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $space = Space::findOne(['id' => 1]);

        $module = \Yii::$app->getModule('xcoin');
        $module->settings->contentContainer($space)->set('allowDirectCoinTransfer', 0);

        $I->amOnSpaceAccountsOverview(1);
        $I->see('Accounts of this space');

        $I->amOnSpaceDefaultAccountDetails(1);
        $I->see('Account overview');

    }

    public function testUserDefaultAccount(FunctionalTester $I)
    {

        $I->wantTo('ensure that user default account works');

        $I->amUser1();

        $I->enableUserModule(2, 'xcoin');

        $I->amOnUserAccountsOverview(2);
        $I->see('Your accounts');

        $I->amOnUserDefaultAccountDetails(2);
        $I->see('Account overview');

    }

    public function testSpaceAccountCreation(FunctionalTester $I)
    {

        $I->wantTo('ensure that space account creation works');

        $I->amUser1();

        $I->enableSpaceModule(2, 'xcoin');

        $space = Space::findOne(['id' => 2]);
        $accountName = 'Test Account';

        $I->sendAjaxGetRequest($space->createUrl('/xcoin/account/edit'));
        $I->sendAjaxPostRequest($space->createUrl('/xcoin/account/edit'), [
            'Account[title]' => $accountName,
        ]);

        $I->SeeRecord(Account::class, [
            'title' => $accountName,
        ]);

    }

    public function testSpaceAccountEditFailing(FunctionalTester $I)
    {

        $I->wantTo('ensure that space account edit failing in some cases');

        $I->amUser1();

        $I->enableSpaceModule(2, 'xcoin');

        $account1 = Account::findOne(['space_id' => 2, 'account_type' => Account::TYPE_DEFAULT]);
        $account2 = Account::findOne(['account_type' => Account::TYPE_ISSUE]);
        $space = Space::findOne(['id' => 2]);

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/account/edit', ['id' => $account1->id]), []);
        $I->seeResponseCodeIs(401);

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/account/edit', ['id' => $account2->id]), []);
        $I->seeResponseCodeIs(401);

    }

    public function testSpaceCrowdfundingAccount(FunctionalTester $I)
    {

        $I->wantTo('ensure that space crowdfunding account exists');

        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $I->amOnSpaceAccountsOverview(1);
        $I->see('Accounts of this space');

        $account = Account::findOne(['space_id' => 1, 'account_type' => Account::TYPE_FUNDING]);

        $I->amOnSpaceAccountDetails($account->id);
        $I->see('Account overview');

    }

    public function testSpaceCrowdfundingTransaction(FunctionalTester $I)
    {

        $I->wantTo('ensure that space crowdfunding transaction exists');

        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $account = Account::findOne(['space_id' => 1, 'account_type' => Account::TYPE_FUNDING]);
        $transaction = Transaction::findOne(['to_account_id' => $account->id]);
        $space = Space::findOne(['id' => 1]);

        $I->sendAjaxGetRequest($space->createUrl('/xcoin/transaction/details', ['id' => $transaction->id]));
        $I->see('Transfer details');

    }

    public function testSpaceAssetDistribution(FunctionalTester $I)
    {

        $I->wantTo('ensure that space asset distribution works');

        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $I->amOnSpaceAccountsOverview(1);
        $I->see('Accounts of this space');

        $space = Space::findOne(['id' => 1]);

        $I->amOnPage($space->createUrl('/xcoin/overview/latest-transactions'));
        $I->see('Latest transactions of all space owned accounts');

        $I->amOnPage($space->createUrl('/xcoin/overview/shareholder-list'));
        $I->see('Shareholder listing');

        $I->amOnPage($space->createUrl('/xcoin/overview/latest-asset-transactions'));
        $I->see('Latest transactions of space assets');

    }

    public function testUserAssetDistribution(FunctionalTester $I)
    {

        $I->wantTo('ensure that user asset distribution works');

        $I->amAdmin();

        $I->enableUserModule(1, 'xcoin');

        $I->amOnUserAccountsOverview(1);
        $I->see('Your accounts');

        $user = User::findOne(['id' => 1]);

        $I->amOnPage($user->createUrl('/xcoin/overview/latest-transactions'));
        $I->see('Latest transactions of all user accounts');

    }

    public function testSpaceAssetCreation(FunctionalTester $I)
    {

        $I->wantTo('ensure that space asset creation works');

        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $I->amOnSpaceAccountsOverview(1);
        $I->see('Accounts of this space');

        $space = Space::findOne(['id' => 1]);
        $asset = Asset::findOne(['space_id' => $space->id]);
        $account = Account::findOne(['space_id' => $space->id, 'account_type' => Account::TYPE_DEFAULT]);

        $I->sendAjaxGetRequest($space->createUrl('/xcoin/asset/issue', ['id' => $asset->id]));
        $I->sendAjaxPostRequest($space->createUrl('/xcoin/asset/issue', ['id' => $asset->id]), [
            'Transaction[to_account_id]' => $account->id,
            'Transaction[amount]' => 20,
            'Transaction[comment]' => 'Testing asset',
        ]);
        $I->seeRecord(Transaction::class, [
            'to_account_id' => $account->id,
            'amount' => 20,
            'comment' => 'Testing asset',
        ]);

    }

    public function testSpaceAccountTransfer(FunctionalTester $I)
    {

        $I->wantTo('ensure that space account transfer works');

        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');
        $I->enableSpaceModule(2, 'xcoin');

        $I->amOnSpaceAccountsOverview(1);
        $I->see('Accounts of this space');

        $space1 = Space::findOne(['id' => 1]);
        $space2 = Space::findOne(['id' => 2]);
        $assetFromSpace1 = Asset::findOne(['space_id' => $space1->id]);
        $accountDefaultSpace1 = Account::findOne(['space_id' => $space1->id, 'account_type' => Account::TYPE_DEFAULT]);
        $accountDefaultSpace2 = Account::findOne(['space_id' => $space2->id, 'account_type' => Account::TYPE_DEFAULT]);

        $I->sendAjaxPostRequest($space1->createUrl('/xcoin/asset/issue', ['id' => $assetFromSpace1->id]), [
            'Transaction[to_account_id]' => $accountDefaultSpace1->id,
            'Transaction[amount]' => 20,
            'Transaction[comment]' => 'Testing asset',
        ]);

        $I->sendAjaxGetRequest($space1->createUrl('/xcoin/transaction/select-account'));
        $I->sendAjaxGetRequest($space1->createUrl('/xcoin/transaction/transfer', ['accountId' => $accountDefaultSpace1->id]));
        $I->sendAjaxPostRequest($space1->createUrl('/xcoin/transaction/transfer', ['accountId' => $accountDefaultSpace1->id]), [
            'Transaction[amount]' => 5,
            'Transaction[asset_id]' => $assetFromSpace1->id,
            'Transaction[to_account_id]' => $accountDefaultSpace2->id,
            'Transaction[comment]' => 'Testing transfer',
        ]);
        $I->seeRecord(Transaction::class, [
            'amount' => 5,
            'asset_id' => $assetFromSpace1->id,
            'to_account_id' => $accountDefaultSpace2->id,
            'comment' => 'Testing transfer',
        ]);

    }

    public function testSpaceAccountEthereumSection(FunctionalTester $I)
    {

        $I->wantTo('ensure that space account ethereum section works');

        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $I->amOnSpaceEthereumOverview(1);
        $I->see('Ethereum');


    }

    public function testAssetAmountWidget1(FunctionalTester $I)
    {

        $I->wantTo('ensure that AssetAmountWidget works (prepare)');
        $I->amGoingTo('Enable the coinsence theme in order to execute the AssetAmount widget');

        $I->amAdmin();


        $I->enableSpaceModule(1, 'xcoin');
        $I->enableUserModule(1, 'xcoin');

        $I->amOnPage(['/admin/setting/design']);
        $I->see('Appearance Settings');
        $I->selectOption('#designsettingsform-theme', 'Coinsence');
        $I->submitForm('.btn.btn-primary', []);
        $I->seeResponseCodeIsSuccessful();

        $spaceToCreate = 'Decimal Space';
        // New space with id = 5
        $I->createSpace($spaceToCreate, 'XcoinSpaceDescription', '#b5810a');
        $I->amGoingTo('create a new space named after defaultAssetName Yii2 parameter');

        $I->amOnPage('/');

    }

    public function testAssetAmountWidget2(FunctionalTester $I)
    {

        $I->wantTo('ensure that AssetAmountWidget works (final)');

        $I->amAdmin();


        $I->enableSpaceModule(1, 'xcoin');
        $I->enableUserModule(1, 'xcoin');

        $I->amOnPage(['/admin/setting/design']);
        $I->see('Appearance Settings');
        $I->selectOption('#designsettingsform-theme', 'Coinsence');
        $I->submitForm('.btn.btn-primary', []);
        $I->seeResponseCodeIsSuccessful();

        $spaceToCreate = 'Decimal Space';
        // New space with id = 5
        $I->createSpace($spaceToCreate, 'XcoinSpaceDescription', '#b5810a');
        $I->amGoingTo('create a new space named after defaultAssetName Yii2 parameter');

        $I->amOnPage('/');

    }

    public function testInvestorAccounting(FunctionalTester $I)
    {

        $I->wantTo('ensure that investor accounting works');

        $space = Space::findOne(['id' => 2]);

        $I->enableSpaceModule($space->id, 'xcoin');

        $I->amUser1();

        $I->amOnSpace($space->id);

        //----------- set space investor setting -----------//

        $I->click('.controls-header .dropdown-navigation .dropdown-toggle');
        $I->see('Modules');

        $I->click('.controls-header .dropdown-navigation ul li:nth-child(4) a');
        $I->see('Space Modules');
        $I->see('Accounting');

        $I->click(['class' => 'configure-module-xcoin']);

        $I->see('Xcoin module configuration');

        $I->fillField('SpaceModuleBasicSettings[accountTitle]', 'Investor');
        $I->fillField('SpaceModuleBasicSettings[transactionAmount]', 50);
        $I->fillField('SpaceModuleBasicSettings[transactionComment]', 'Contributor transaction');

        $I->click('Save');

        //----------- Member Add Event -----------//

        $I->switchIdentity('User3');

        $user = User::findOne(['id' => 4]);

        $I->amOnSpace($space->id);

        $I->click('#requestMembershipButton');

        MemberEvent::trigger(Membership::class, Membership::EVENT_MEMBER_ADDED, new MemberEvent([
            'space' => $space, 'user' => $user
        ]));

        $I->seeRecord(Account::class, [
            'space_id' => $space->id,
            'user_id' => $user->id,
            'title' => 'Investor',
            'account_type' => Account::TYPE_COMMUNITY_INVESTOR,
            'investor_id' => $user->id
        ]);

        $I->seeRecord(Transaction::class, [
            'amount' => 50,
            'transaction_type' => Transaction::TRANSACTION_TYPE_ISSUE,
        ]);

        //----------- Member Remove Event -----------//

        $I->switchIdentity('User1');

        $I->sendAjaxPostRequest($space->createUrl('space/manage/member/remove', [
            'userGuid' => $user->guid
        ]));

        MemberEvent::trigger(Membership::class, Membership::EVENT_MEMBER_REMOVED, new MemberEvent([
            'space' => $space, 'user' => $user
        ]));

        $I->seeRecord(Account::class, [
            'space_id' => $space->id,
            'user_id' => null,
            'account_type' => Account::TYPE_COMMUNITY_INVESTOR,
            'investor_id' => $user->id
        ]);

    }

}
