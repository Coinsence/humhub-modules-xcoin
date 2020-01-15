<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\xcoin\tests\codeception\fixtures;

use humhub\modules\xcoin\models\Asset;
use yii\test\ActiveFixture;

class AssetFixture extends ActiveFixture
{

    public $tableName = 'xcoin_asset';
    public $modelClass = Asset::class;

}
