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

use humhub\modules\xcoin\models\Funding;
use yii\test\ActiveFixture;

class FundingFixture extends ActiveFixture
{

    public $tableName = 'xcoin_funding';
    public $modelClass = Funding::class;

}
