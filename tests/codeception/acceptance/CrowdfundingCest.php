<?php

namespace user\acceptance;

use xcoin\AcceptanceTester;

class CrowdfundingCest
{

    public function testPageVisibility(AcceptanceTester $I)
    {

        $I->wantTo('ensure that crowdfunding page works');
        $I->amOnCrowdfunding();
        $I->see('Crowd Funding');

    }

    public function testAddingProject(AcceptanceTester $I)
    {

        $I->wantTo('ensure that adding crowfunding project works');

        $spaceFrom = 'XcoinSpace 1';
        $spaceTo = 'XcoinSpace 2';

        $I->amUser1();
        $I->createSpace($spaceFrom, 'XcoinSpaceDescription');
        $I->createSpace($spaceTo, 'XcoinSpaceDescription');

        $I->amOnCrowdfunding();
        $I->see('Crowd Funding');
        $I->click('Add Your Project');

        $I->waitForText('Select from which space you want to create your campaign', 30, '#globalModal');
        $I->executeJS('$("#funding-space_id").select2("open")');
        $I->waitForElementVisible('.select2-container--open');
        $I->see($spaceFrom, '.select2-container--open');
        $I->see($spaceTo, '.select2-container--open');
        $I->click($spaceFrom);
        $I->click('Next');

        $I->waitForText('Set funding request', 30, '#globalModal');
        $I->executeJS('$("#funding-asset_id").select2("open")');
        $I->waitForElementVisible('.select2-container--open');
        $I->see($spaceTo, '.select2-container--open');
        $I->click($spaceTo);
        $I->click('Next');

        $I->waitForText('Provide details', 30, '#globalModal');
        $I->fillField('Funding[amount]', '20');
        // $I->fillField('Funding[exchange_rate]', '1');
        $I->fillField('Funding[title]', 'Testing Project');
        $I->fillField('Funding[deadline]', '12/30/20');
        $I->fillField('Funding[description]', 'ProjectDescription');
        $I->executeJS('$("#funding-content .humhub-ui-richtext p").text("ProjectFullDescription")');
        $I->click('Next');

        $I->waitForText('Gallery', 30, '#globalModal');
        $I->click('Save');







    }

}
