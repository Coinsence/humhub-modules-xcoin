<?php

namespace humhub\modules\xcoin\helpers;

use humhub\modules\user\components\PermissionManager;
use humhub\modules\xcoin\permissions\ReviewSubmittedProjects;
use yii\base\InvalidConfigException;

/**
 * PublicOffersHelper
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class PublicOffersHelper
{
    /**
     * @return bool
     * @throws InvalidConfigException
     */
    public static function canReviewSubmittedProjects()
    {
        return (new PermissionManager())->can(new ReviewSubmittedProjects());
    }
}
