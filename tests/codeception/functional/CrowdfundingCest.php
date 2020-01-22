<?php

/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Ghanmi Mortadha <mortadha.ghanmi56@gmail.com >
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace user\functional;

use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\permissions\ReviewPublicOffers;
use xcoin\FunctionalTester;

class CrowdfundingCest
{
    // TODO: invest in a project

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

        $I->sendAjaxPostRequest('index.php?r=xcoin/funding-overview/new', []);
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

        $I->enableModule(1, 'xcoin');

        $I->amUser1();
        $I->setGroupPermission(2, ReviewPublicOffers::class);

        $project = Funding::findOne(['id' => 1]);

        $I->amOnCrowdfunding();
        $I->see($project->title);

        $I->amOnProject($project->id);
        $I->see($project->title);

        $I->click(['class' => 'review-btn-trusted']);

        $I->amOnCrowdfunding(['verified' => 1]);
        $I->see($project->title);
    }
}
