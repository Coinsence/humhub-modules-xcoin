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

class PermissionsCest
{
    public function testPermissionsDisplay(FunctionalTester $I)
    {
        $I->wantTo('ensure that permissions are shown correctly');
        $I->amUser1();

        $I->amOnSpace(2);

        $I->click('.controls-header .dropdown-navigation .dropdown-toggle');
        $I->see('Security');

        $I->click('.controls-header .dropdown-navigation ul li:nth-child(2) a');
        $I->see('Security settings');
        $I->see('Permissions');

        $I->click('.tab-menu ul li:nth-child(2) a');
        $I->see('Create account');
        $I->see('Review Public Offers');

    }
}
