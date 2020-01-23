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

use xcoin\FunctionalTester;

class AccountingCest
{

    public function testSpaceAccounts(FunctionalTester $I)
    {

        $I->wantTo('ensure that space accounts exists and works');

        $I->amUser1();

        // New space with id = 5
        $I->createSpace('XcoinSpace 1', 'XcoinSpaceDescription', '#b5810a');

        $I->amOnSpaceAccountOverview(5);
        $I->see('Accounts of this space');

    }

    public function testUserAccounts(FunctionalTester $I)
    {

        $I->wantTo('ensure that user accounts exists and works');

        $I->amUser1();

        $I->enableUserModule(2, 'xcoin');

        $I->amOnUserAccountOverview(2);
        $I->see('Your accounts');

    }

}
