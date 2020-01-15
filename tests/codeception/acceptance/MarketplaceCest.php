<?php

namespace user\acceptance;

use xcoin\AcceptanceTester;

class MarketplaceCest
{

    public function testPageVisibility(AcceptanceTester $I)
    {

        $I->wantTo('ensure that marketplace page works');
        $I->amOnMarketplace();
        $I->see('Marketplace');

    }

}
