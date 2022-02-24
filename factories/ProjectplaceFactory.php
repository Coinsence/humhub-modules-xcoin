<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2022 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\factories;

use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\Projectplace;

class ProjectplaceFactory
{
    public static function createNew()
    {
        return new Projectplace();
    }

    public static function createNewForSpace(Space $space)
    {
        $projectplace = self::createNew();

        $projectplace->scenario = Projectplace::SCENARIO_INSERT;
        $projectplace->space_id = $space->id;

        return $projectplace;
    }
}