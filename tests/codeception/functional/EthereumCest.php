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

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Account;
use xcoin\FunctionalTester;

class EthereumCest
{
    public function testSpaceEthereumSection(FunctionalTester $I)
    {

        $I->wantTo('ensure that space account ethereum section works');

        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $I->amOnSpaceEthereumOverview(1);
        $I->see('Ethereum');


    }

    public function testSpaceEthereumEnabling(FunctionalTester $I)
    {

        $I->wantTo('ensure that space ethereum enabling works');

        $I->amAdmin();

        $space = Space::findOne(['id' => 1]);

        $I->enableSpaceModule($space->id, 'xcoin');

        $I->amOnSpaceEthereumOverview($space->id);
        $I->see('Enable Ethereum');

        $I->click(['id' => 'ether-enable-btn']);

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/ethereum/enable'));

        $I->seeRecord(Space::class, [
            'id' => $space->id,
            'eth_status' => Space::ETHEREUM_STATUS_IN_PROGRESS
        ]);

        $I->canSeeResponseCodeIs(200);
    }


    public function testSpaceEthereumTransactionsMigration(FunctionalTester $I)
    {

        $I->wantTo('ensure that space ethereum transactions migration works');

        $I->amAdmin();

        $space = Space::findOne(['id' => 1]);
        $space->updateAttributes(['eth_status' => Space::ETHEREUM_STATUS_ENABLED]);

        $I->enableSpaceModule($space->id, 'xcoin');

        $I->amOnSpaceEthereumOverview($space->id);
        $I->see('Migrate missing transactions');


        $I->sendAjaxPostRequest($space->createUrl('/xcoin/ethereum/migrate-transactions'));

        $I->canSeeResponseCodeIs(200);
    }

    public function testSpaceEthereumBalancesSynchronization(FunctionalTester $I)
    {

        $I->wantTo('ensure that space ethereum synchronize balances works');

        $I->amAdmin();

        $space = Space::findOne(['id' => 1]);
        $space->updateAttributes(['eth_status' => Space::ETHEREUM_STATUS_ENABLED]);

        $I->enableSpaceModule($space->id, 'xcoin');

        $I->amOnSpaceEthereumOverview($space->id);
        $I->see('Synchronize Balances');


        $I->sendAjaxPostRequest($space->createUrl('/xcoin/ethereum/synchronize-balances'));

        $I->canSeeResponseCodeIs(200);
    }

    public function testUserEthereumLoadAccountPrivateKey(FunctionalTester $I)
    {

        $I->wantTo('ensure that loading user account private key works');

        $I->amUser1();

        $user = User::findOne(['id' => 2]);
        $userDefaultAccount = Account::findOne([
            'user_id' => $user->id,
            'account_type' => Account::TYPE_DEFAULT
        ]);

        $I->enableUserModule($user->id, 'xcoin');

        $I->amOnUserAccountsOverview(2);
        $I->see('Your accounts');


        $I->sendAjaxPostRequest($user->createUrl('/xcoin/ethereum/load-private-key', ['accountId' => $userDefaultAccount->id]));

        // just making sure that the popup password appears
        // no need to fill field and continue because ethereum is disabled
        $I->see('Current Password');
    }
}
