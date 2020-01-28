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
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\Transaction;
use humhub\modules\xcoin\permissions\ReviewPublicOffers;
use xcoin\FunctionalTester;

class CrowdfundingCest
{
    // TODO: use createUrl method to create urls instead of hard-coding it

    public function testPageVisibility(FunctionalTester $I)
    {

        $I->wantTo('ensure that crowdfunding page works');
        $I->amOnCrowdfunding();
        $I->see('Crowd Funding');

    }

    public function testProjectCreation(FunctionalTester $I)
    {

        $I->amUser1();

        $I->wantTo('ensure that project creation works');

        $spaceFrom = 'XcoinSpace 1';
        $spaceTo = 'XcoinSpace 2';

        $projectAmount = 20;
        $projectTitle = 'Testing Project';
        $projectDeadline = '12/30/20';
        $projectDescription = 'ProjectDescription';
        $projectFullDescription = 'ProjectFullDescription';

        // New space with id = 5
        $I->createSpace($spaceFrom, 'XcoinSpaceDescription', '#b5810a');
        $spaceFromId = 5;

        // New space with id = 6
        $I->createSpace($spaceTo, 'XcoinSpaceDescription', '#a0185b');
        $spaceToId = 6;

        $asset = Asset::findOne(['space_id' => $spaceToId]);

        $I->amOnSpaceProjects($spaceFromId);
        $I->see('Currently there are no open funding requests.');

        $I->sendAjaxGetRequest('index.php?r=xcoin/funding-overview/new');
        $I->sendAjaxPostRequest('index.php?r=xcoin/funding-overview/new', [
            'step' => -1,
            'Funding[space_id]' => $spaceFromId,
        ]);
        $I->sendAjaxPostRequest('index.php?r=xcoin/funding-overview/new', [
            'step' => 1,
            'Funding[space_id]' => $spaceFromId,
            'Funding[asset_id]' => $asset->id,
        ]);
        $I->sendAjaxPostRequest('index.php?r=xcoin/funding-overview/new', [
            'step' => 2,
            'Funding[space_id]' => $spaceFromId,
            'Funding[asset_id]' => $asset->id,
            'Funding[amount]' => $projectAmount,
            'Funding[exchange_rate]' => 1,
            'Funding[rate]' => 1,
            'Funding[title]' => $projectTitle,
            'Funding[deadline]' => $projectDeadline,
            'Funding[description]' => $projectDescription,
            'Funding[content]' => $projectFullDescription,
        ]);
        $I->sendAjaxPostRequest('index.php?r=xcoin/funding-overview/new', [
            'step' => 3,
            'Funding[space_id]' => $spaceFromId,
            'Funding[asset_id]' => $asset->id,
            'Funding[amount]' => $projectAmount,
            'Funding[exchange_rate]' => 1,
            'Funding[rate]' => 1,
            'Funding[title]' => $projectTitle,
            'Funding[deadline]' => $projectDeadline,
            'Funding[description]' => $projectDescription,
            'Funding[content]' => $projectFullDescription,
        ]);
        $I->seeRecord(Funding::class, [
            'space_id' => $spaceFromId,
            'asset_id' => $asset->id,
            'exchange_rate' => 1,
            'amount' => $projectAmount,
            'title' => $projectTitle,
            'description' => $projectDescription,
            'content' => $projectFullDescription
        ]);

        $projectId = 2;

        $I->amOnCrowdfunding();
        $I->see($projectTitle);

        $I->amOnProject($projectId);
        $I->see($projectTitle);

        $I->amOnProjectSpace($projectId);
        $I->see($projectTitle);

    }

    public function testProjectReview(FunctionalTester $I)
    {
        $I->wantTo('ensure that project review works');

        $spaceProjectOwnerId = 1;

        $I->amUser1();

        $I->setGroupPermission(2, ReviewPublicOffers::class);

        $project = Funding::findOne(['id' => 1]);

        $I->amOnCrowdfunding();
        $I->see($project->title);

        // Enabling xcoin module for the space before accessing its project/projects page
        $I->enableSpaceModule($spaceProjectOwnerId, 'xcoin');
        $I->amOnProject($project->id);
        $I->see($project->title);

        $I->click(['class' => 'review-btn-trusted']);

        $I->amOnCrowdfunding(['verified' => Funding::FUNDING_REVIEWED]);
        $I->see($project->title);
    }

    public function testProjectEdit(FunctionalTester $I)
    {

        $I->wantTo('ensure that project edit works');

        $I->amAdmin();

        $owner = Space::findOne(['id' => 1]);
        $funding = Funding::findOne(['id' => 1]); // this funding is created by Space 1 owner by Admin

        $I->enableSpaceModule($owner->id, 'xcoin');

        $I->sendAjaxGetRequest('index.php?r=xcoin/funding/edit&id='.$funding->id.'&cguid='.$owner->guid);
        $I->sendAjaxPostRequest('index.php?r=xcoin/funding/edit&id='.$funding->id.'&cguid='.$owner->guid, [
            'step' => 2,
            'Funding[space_id]' => $funding->space_id,
            'Funding[asset_id]' => $funding->asset_id,
            'Funding[amount]' => $funding->amount,
            'Funding[exchange_rate]' => $funding->exchange_rate,
            'Funding[rate]' => $funding->rate,
            'Funding[title]' => $funding->title,
            'Funding[deadline]' => $funding->deadline,
            'Funding[description]' => $funding->description,
            'Funding[content]' => $funding->content,
        ]);
        $I->sendAjaxPostRequest('index.php?r=xcoin/funding/edit&id='.$funding->id.'&cguid='.$owner->guid, [
            'step' => 3,
            'Funding[space_id]' => $funding->space_id,
            'Funding[asset_id]' => $funding->asset_id,
            'Funding[amount]' => $funding->amount,
            'Funding[exchange_rate]' => $funding->exchange_rate,
            'Funding[rate]' => $funding->rate,
            'Funding[title]' => $funding->title.'_mod',
            'Funding[deadline]' => $funding->deadline,
            'Funding[description]' => $funding->description.'_mod',
            'Funding[content]' => $funding->content,
        ]);
        $I->seeRecord(Funding::class, [
            'space_id' => $funding->space_id,
            'asset_id' => $funding->asset_id,
            'exchange_rate' => $funding->exchange_rate,
            'amount' => $funding->amount,
            'title' => $funding->title.'_mod',
            'description' => $funding->description.'_mod',
            'content' => $funding->content
        ]);

    }

    public function testProjectEditFailure(FunctionalTester $I)
    {

        $I->wantTo('ensure that project edit fails');

        $I->amAdmin();

        $owner = Space::findOne(['id' => 1]);
        $funding = Funding::findOne(['id' => 1]); // this funding is created by Space 1 owner by Admin

        $I->enableSpaceModule($owner->id, 'xcoin');

        $I->sendAjaxGetRequest('index.php?r=xcoin/funding/edit&id='.$funding->id.'&cguid='.$owner->guid);
        $I->sendAjaxPostRequest('index.php?r=xcoin/funding/edit&id='.$funding->id.'&cguid='.$owner->guid, [
            'step' => 2,
            'Funding[space_id]' => $funding->space_id,
            'Funding[asset_id]' => $funding->asset_id,
            'Funding[amount]' => 0,
            'Funding[exchange_rate]' => $funding->exchange_rate,
            'Funding[rate]' => $funding->rate,
            'Funding[title]' => $funding->title,
            'Funding[deadline]' => $funding->deadline,
            'Funding[description]' => $funding->description,
            'Funding[content]' => $funding->content,
        ]);
        $I->sendAjaxPostRequest('index.php?r=xcoin/funding/edit&id='.$funding->id.'&cguid='.$owner->guid, [
            'step' => 3,
            'Funding[space_id]' => $funding->space_id,
            'Funding[asset_id]' => $funding->asset_id,
            'Funding[amount]' => 0,
            'Funding[exchange_rate]' => $funding->exchange_rate,
            'Funding[rate]' => $funding->rate,
            'Funding[title]' => $funding->title.'_mod',
            'Funding[deadline]' => $funding->deadline,
            'Funding[description]' => $funding->description.'_mod',
            'Funding[content]' => $funding->content,
        ]);
        $I->dontSeeRecord(Funding::class, [
            'space_id' => $funding->space_id,
            'asset_id' => $funding->asset_id,
            'exchange_rate' => $funding->exchange_rate,
            'amount' => 0,
            'title' => $funding->title.'_mod',
            'description' => $funding->description.'_mod',
            'content' => $funding->content
        ]);

    }

    public function testProjectCancel(FunctionalTester $I)
    {

        $I->wantTo('ensure that project cancelling works');

        $I->amAdmin();

        $owner = Space::findOne(['id' => 1]);
        $funding = Funding::findOne(['id' => 1]); // this funding is created by Space 1 owner by Admin

        $I->enableSpaceModule($owner->id, 'xcoin');

        $I->sendAjaxGetRequest('index.php?r=xcoin/funding/cancel&id='.$funding->id.'&cguid='.$owner->guid);
        $I->dontSeeRecord(Funding::class, [
            'space_id' => $funding->space_id,
            'asset_id' => $funding->asset_id,
            'exchange_rate' => $funding->exchange_rate,
            'amount' => $funding->amount,
            'title' => $funding->title,
            'description' => $funding->description,
            'content' => $funding->content
        ]);

    }

    public function testProjectInvestmentAcceptance(FunctionalTester $I)
    {

        $I->wantTo('ensure that project investment acceptance works');

        $I->amAdmin();

        $owner = Space::findOne(['id' => 1]);
        $funding = Funding::findOne(['id' => 1]); // this funding is created by Space 1 owner by Admin

        $I->enableSpaceModule($owner->id, 'xcoin');

        $I->sendAjaxGetRequest('index.php?r=xcoin/funding/accept&id='.$funding->id.'&cguid='.$owner->guid);
        $I->SeeRecord(Funding::class, [
            'space_id' => $funding->space_id,
            'asset_id' => $funding->asset_id,
            'exchange_rate' => $funding->exchange_rate,
            'amount' => $funding->amount,
            'title' => $funding->title,
            'description' => $funding->description,
            'content' => $funding->content,
            'status' => Funding::FUNDING_STATUS_INVESTMENT_ACCEPTED
        ]);

    }

    public function testProjectInvestment(FunctionalTester $I)
    {

        $I->wantTo('ensure that project investment works');

        $I->amUser1();

        $owner = Space::findOne(['id' => 1]);
        $funding = Funding::findOne(['id' => 1]); // this funding is created by Space 1 owner by Admin
        $investorAccount = Account::findOne(['user_id' => 2, 'account_type' => Account::TYPE_DEFAULT]);
        $fundingAccount = Account::findOne(['space_id' => 1, 'account_type' => Account::TYPE_FUNDING]);

        $amountToInvest = 10;

        $I->enableSpaceModule(1, 'xcoin');
        $I->enableUserModule(2, 'xcoin');

        $I->sendAjaxGetRequest($owner->createUrl('/xcoin/funding/invest', ['fundingId' => '']));
        $I->seeResponseCodeIs(404);

        $I->sendAjaxGetRequest($owner->createUrl('/xcoin/funding/invest', ['fundingId' => $funding->id]));
        $I->sendAjaxGetRequest($owner->createUrl('/xcoin/funding/invest', ['fundingId' => $funding->id, 'accountId' => $investorAccount->id]));
        $I->sendAjaxPostRequest($owner->createUrl('/xcoin/funding/invest', ['fundingId' => $funding->id, 'accountId' => $investorAccount->id]), [
            'FundingInvest[amountPay]' => $amountToInvest,
            'FundingInvest[amountBuy]' => 1,
        ]);
        $I->seeRecord(Transaction::class, [
            'from_account_id' => $investorAccount->id,
            'to_account_id' => $fundingAccount->id,
            'amount' => $amountToInvest
        ]);
        $I->seeRecord(Transaction::class, [
            'from_account_id' => $fundingAccount->id,
            'to_account_id' => $investorAccount->id,
            'amount' => $amountToInvest
        ]);

    }

}
