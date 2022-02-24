<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2022 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\repositories;

use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\Projectplace;

class ProjectplaceRepository
{
    public static function findAllForSpace(Space $space, $returnOnlyQuery = false)
    {
        $projectPlacesQuery = Projectplace::find()
            ->where(['space_id' => $space->id])
            ->orderBy(['created_at' => SORT_DESC]);
        
        return $returnOnlyQuery ? $projectPlacesQuery : $projectPlacesQuery->all();
    }
}