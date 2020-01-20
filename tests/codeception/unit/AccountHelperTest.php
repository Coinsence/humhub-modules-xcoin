<?php

namespace tests\codeception\unit\modules\xcoin;

use humhub\modules\user\models\User;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\models\Account;
use humhub\modules\space\models\Space;
use xcoin\XcoinTestCase;
use yii\db\ActiveQuery;

class AccountHelperTest extends XcoinTestCase
{
    public function testCreateSpaceDefaultAccount()
    {
        // loading space 4 which will not have a default account attached
        // other spaces will have default accounts when setting fixtures to be ready to use in other test cases
        $space = Space::findOne(['id' => 4]);

        $defaultAccount = Account::findOne(['space_id' => $space->id, 'account_type' => Account::TYPE_DEFAULT]);

        $this->assertNull($defaultAccount);

        AccountHelper::initContentContainer($space);

        $defaultAccount = Account::findOne(['space_id' => $space->id, 'account_type' => Account::TYPE_DEFAULT]);

        $this->assertNotNull($defaultAccount);
    }

    public function testCreateUserDefaultAccount()
    {
        // loading user 4 which will not have a default account attached
        // other users will have default accounts when setting fixtures to be ready to use in other test cases
        $space = $user = User::findOne(['id' => 4]);;

        // space_id must be null , because when user is managing a space account space_id will be set
        $defaultAccount = Account::findOne([
            'user_id' => $space->id,
            'account_type' => Account::TYPE_DEFAULT,
            'space_id' => null
        ]);

        $this->assertNull($defaultAccount);

        AccountHelper::initContentContainer($space);

        $defaultAccount = Account::findOne([
            'user_id' => $space->id,
            'account_type' => Account::TYPE_DEFAULT,
            'space_id' => null
        ]);

        $this->assertNotNull($defaultAccount);
    }

    public function testGetSpaceAccountsQuery()
    {
        $space = Space::findOne(['id' => 1]);

        /** @var ActiveQuery $query */
        $query = AccountHelper::getAccountsQuery($space);

        // space 1 have at least default account
        $this->assertNotNull($query);

        $this->assertEquals(Account::class, $query->modelClass);
    }

    public function testGetSpaceAccounts()
    {
        $space = Space::findOne(['id' => 1]);

        $accountArray = AccountHelper::getAccounts($space);

        // space 1 have at least default account
        $this->assertNotEmpty($accountArray);

        $this->assertInstanceOf(Account::class, $accountArray[0]);
        $this->assertNull($accountArray[0]->user);
        $this->assertEquals($accountArray[0]->space_id, $space->id);
        $this->assertEquals($accountArray[0]->account_type, Account::TYPE_DEFAULT);
    }
}
