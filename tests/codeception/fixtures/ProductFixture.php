<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\xcoin\tests\codeception\fixtures;

use humhub\modules\xcoin\models\Product;
use yii\test\ActiveFixture;

class ProductFixture extends ActiveFixture
{

    public $tableName = 'xcoin_product';
    public $modelClass = Product::class;

}
