<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\xcoin\tests\codeception\fixtures;

use humhub\modules\xcoin\models\Funding;
use yii\test\ActiveFixture;

class FundingFixture extends ActiveFixture
{

    public $tableName = 'xcoin_funding';
    public $modelClass = Funding::class;

}
