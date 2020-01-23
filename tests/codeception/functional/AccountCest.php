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
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\permissions\ReviewPublicOffers;
use xcoin\FunctionalTester;

class AccountCest
{
    public function testAccountOverview(FunctionalTester $I)
    {

        $I->wantTo('ensure that account overview page works');

        $I->amUser1();

        $I->enableSpaceModule(2, 'xcoin');

        $I->amOnSpaceAccountOverview(2);

        $I->click('tbody tr:first-child td:last-child a:last-child');
    }

    public function testAccountCreation(FunctionalTester $I)
    {

        $I->wantTo('ensure that account creation works');

        $I->amUser1();

        $I->enableSpaceModule(2, 'xcoin');

        $space = Space::findOne(['id' => 2]);
        $accountName = 'Test Account';

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/account/edit', ['container' => $space]), [
            'Account[title]' => $accountName,
        ]);

        $I->SeeRecord(Account::class, [
            'title' => $accountName,
        ]);

    }
}
