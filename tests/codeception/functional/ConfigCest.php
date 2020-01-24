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

use humhub\modules\content\models\ContentContainerSetting;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\Utils;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Transaction;
use xcoin\FunctionalTester;

class ConfigCest
{
    public function testPageVisibility(FunctionalTester $I)
    {
        $I->wantTo('ensure that config page works');
        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $I->amOnSpaceConfig(1);
        $I->see('Xcoin module configuration');
        $I->see('Space coins allocation transaction parameters');

        $I->amOnSpaceConfig(1, 'schedule');
        $I->see('Space scheduled transactions parameters');

        $I->amOnSpaceConfig(1, 'manual');
        $I->see('Select users to allocate coins to');

    }

    public function testBasicConfig(FunctionalTester $I)
    {

        $I->wantTo('ensure that basic config settings works');
        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $coinAllocationAccountTitle = 'Xcoin_testing';
        $coinAllocationTransactionAmount = 5;
        $coinAllocationTransactionComment = 'Xcoin_testing regular allocation';

        $space = Space::findOne(['id' => 1]);

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/config/index'), [
            'SpaceModuleBasicSettings[accountTitle]' => $coinAllocationAccountTitle,
            'SpaceModuleBasicSettings[transactionAmount]' => $coinAllocationTransactionAmount,
            'SpaceModuleBasicSettings[transactionComment]' => $coinAllocationTransactionComment,
            'SpaceModuleBasicSettings[allowDirectCoinTransfer]' => 1
        ]);
        $I->seeRecord(ContentContainerSetting::class, [
            'contentcontainer_id' => $space->contentcontainer_id,
            'name' => 'accountTitle',
            'value' => 'Xcoin_testing',
        ]);
        $I->seeRecord(ContentContainerSetting::class, [
            'contentcontainer_id' => $space->contentcontainer_id,
            'name' => 'transactionAmount',
            'value' => 5,
        ]);
        $I->seeRecord(ContentContainerSetting::class, [
            'contentcontainer_id' => $space->contentcontainer_id,
            'name' => 'transactionComment',
            'value' => 'xcoin_testing regular allocation',
        ]);
        $I->seeRecord(ContentContainerSetting::class, [
            'contentcontainer_id' => $space->contentcontainer_id,
            'name' => 'allowDirectCoinTransfer',
            'value' => 1,
        ]);

    }

    public function testScheduleConfig(FunctionalTester $I)
    {

        $I->wantTo('ensure that schedule config settings works');
        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $space = Space::findOne(['id' => 1]);

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/config/schedule'), [
            'SpaceModuleScheduleSettings[transactionPeriod]' => Utils::TRANSACTION_PERIOD_MONTHLY,
        ]);
        $I->seeRecord(ContentContainerSetting::class, [
            'contentcontainer_id' => $space->contentcontainer_id,
            'name' => 'transactionPeriod',
            'value' => Utils::TRANSACTION_PERIOD_MONTHLY,
        ]);
        $I->amOnSpaceConfig(1, 'schedule');

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/config/schedule'), [
            'SpaceModuleScheduleSettings[transactionPeriod]' => Utils::TRANSACTION_PERIOD_WEEKLY,
        ]);
        $I->seeRecord(ContentContainerSetting::class, [
            'contentcontainer_id' => $space->contentcontainer_id,
            'name' => 'transactionPeriod',
            'value' => Utils::TRANSACTION_PERIOD_WEEKLY,
        ]);
        $I->amOnSpaceConfig(1, 'schedule');

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/config/schedule'), [
            'SpaceModuleScheduleSettings[transactionPeriod]' => Utils::TRANSACTION_PERIOD_NONE,
        ]);
        $I->seeRecord(ContentContainerSetting::class, [
            'contentcontainer_id' => $space->contentcontainer_id,
            'name' => 'transactionPeriod',
            'value' => Utils::TRANSACTION_PERIOD_NONE,
        ]);
        $I->amOnSpaceConfig(1, 'schedule');

    }

    public function testManualAllocationForAllMembers(FunctionalTester $I)
    {

        $I->wantTo('ensure that manual allocation for all members works');
        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $coinAllocationAccountTitle = 'Xcoin_testing';
        $coinAllocationTransactionAmount = 5;
        $coinAllocationTransactionComment = 'Xcoin_testing regular allocation';

        $userAccountIdToGetCoinAllocation = 10;

        $space = Space::findOne(['id' => 1]);
        $spaceIssueAccount = Account::findOne(['space_id' => $space->id, 'account_type' => Account::TYPE_ISSUE]);

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/config/manual'), [
            'SpaceModuleManualSettings[selectAllMembers]' => 1,
        ]);

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/config/index'), [
            'SpaceModuleBasicSettings[accountTitle]' => $coinAllocationAccountTitle,
            'SpaceModuleBasicSettings[transactionAmount]' => $coinAllocationTransactionAmount,
            'SpaceModuleBasicSettings[transactionComment]' => $coinAllocationTransactionComment,
            'SpaceModuleBasicSettings[allowDirectCoinTransfer]' => 1
        ]);

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/config/manual'), [
            'SpaceModuleManualSettings[selectAllMembers]' => 1,
        ]);
        $I->seeRecord(Transaction::class, [
            'transaction_type' => Transaction::TRANSACTION_TYPE_ISSUE,
            'to_account_id' => $space->id,
            'from_account_id' => $spaceIssueAccount->id,
            'amount' => $coinAllocationTransactionAmount,
            'comment' => 'Issue transaction Amount to default Account'
        ]);
        $I->seeRecord(Transaction::class, [
            'transaction_type' => Transaction::TRANSACTION_TYPE_TRANSFER,
            'to_account_id' => $userAccountIdToGetCoinAllocation,
            'from_account_id' => $space->id,
            'amount' => $coinAllocationTransactionAmount,
            'comment' => $coinAllocationTransactionComment
        ]);

    }
    public function testManualAllocationForSpecificMembers(FunctionalTester $I)
    {

        $I->wantTo('ensure that manual allocation for specific members works');
        $I->amAdmin();

        $I->enableSpaceModule(1, 'xcoin');

        $coinAllocationAccountTitle = 'Xcoin_testing';
        $coinAllocationTransactionAmount = 5;
        $coinAllocationTransactionComment = 'Xcoin_testing regular allocation';

        $userAccountIdToGetCoinAllocation = 10;

        $space = Space::findOne(['id' => 1]);
        $spaceIssueAccount = Account::findOne(['space_id' => $space->id, 'account_type' => Account::TYPE_ISSUE]);

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/config/index'), [
            'SpaceModuleBasicSettings[accountTitle]' => $coinAllocationAccountTitle,
            'SpaceModuleBasicSettings[transactionAmount]' => $coinAllocationTransactionAmount,
            'SpaceModuleBasicSettings[transactionComment]' => $coinAllocationTransactionComment,
            'SpaceModuleBasicSettings[allowDirectCoinTransfer]' => 1
        ]);

        $selectedMembers = ['01e50e0d-82cd-41fc-8b0c-552392f5839e']; // User2 Investor account guid

        $I->sendAjaxPostRequest($space->createUrl('/xcoin/config/manual'), [
            'SpaceModuleManualSettings[selectAllMembers]' => 0,
            'SpaceModuleManualSettings[selectedMembers]' => $selectedMembers,
        ]);

        $I->seeRecord(Transaction::class, [
            'transaction_type' => Transaction::TRANSACTION_TYPE_ISSUE,
            'to_account_id' => $space->id,
            'from_account_id' => $spaceIssueAccount->id,
            'amount' => $coinAllocationTransactionAmount,
            'comment' => 'Issue transaction Amount to default Account'
        ]);
        $I->seeRecord(Transaction::class, [
            'transaction_type' => Transaction::TRANSACTION_TYPE_TRANSFER,
            'to_account_id' => $userAccountIdToGetCoinAllocation,
            'from_account_id' => $space->id,
            'amount' => $coinAllocationTransactionAmount,
            'comment' => $coinAllocationTransactionComment
        ]);

    }

}
