<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\xcoin\tests\codeception\fixtures;

use humhub\modules\xcoin\models\Account;
use yii\test\ActiveFixture;

class AccountFixture extends ActiveFixture
{

    public $tableName = 'xcoin_account';
    public $modelClass = Account::class;

    // TODO: revisit dependencies format
    public $depends = [
        AssetFixture::class,
        ExchangeFixture::class,
        FundingFixture::class,
        ProductFixture::class,
        TransactionFixture::class,
    ];

}
