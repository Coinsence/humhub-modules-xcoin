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
use xcoin\FunctionalTester;

class AjaxCest
{
    public function testGetAccounts(FunctionalTester $I)
    {
        $I->wantTo('ensure that "/xcoin/ajax/get-account" Api endpoint works');
        $I->amUser1();

        $user = User::findOne(['id' => 2]);

        $I->sendAjaxGetRequest($user->createUrl('/xcoin/ajax/get-accounts'));
        $I->seeResponseCodeIsSuccessful();

    }

    public function testGetSubAccounts(FunctionalTester $I)
    {
        $I->wantTo('ensure that "/xcoin/ajax/get-sub-accounts" Api endpoint works');
        $I->amUser1();

        $user = User::findOne(['id' => 2]);

        $I->sendAjaxPostRequest($user->createUrl('/xcoin/ajax/get-sub-accounts'), [
            'id' => 1
        ]);
        $I->seeResponseCodeIsSuccessful();

    }

    public function testMemberSearch(FunctionalTester $I)
    {
        $I->wantTo('ensure that "/xcoin/member-search/json" Api endpoint works');
        $I->amAdmin();

        $space = Space::findOne(['id' => 1]);
        $I->enableSpaceModule(1, 'xcoin');

        $I->sendAjaxGetRequest($space->createUrl('/xcoin/member-search/json', ['keyword' => 'Adm']));
        $I->seeResponseCodeIsSuccessful();

    }

}
