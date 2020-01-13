<?php

namespace user\acceptance;

use xcoin\AcceptanceTester;

class CrowdfundingCest
{

    public function testCrowdfundingVisibility(AcceptanceTester $I)
    {

        $I->wantTo('ensure that crowdfunding page works');
        $I->amOnCrowdfunding();
        $I->see('Crowd Funding');

    }

}
