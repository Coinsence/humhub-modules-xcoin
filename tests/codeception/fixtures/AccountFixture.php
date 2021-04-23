<?php

/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Ghanmi Mortadha <mortadha.ghanmi56@gmail.com >
 * @author Daly Ghaith <daly.ghaith@gmail.com>
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
