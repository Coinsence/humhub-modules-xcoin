<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\xcoin\tests\codeception\fixtures;

use humhub\modules\xcoin\models\Transaction;
use yii\test\ActiveFixture;

class TransactionFixture extends ActiveFixture
{

    public $tableName = 'xcoin_transaction';
    public $modelClass = Transaction::class;

}
