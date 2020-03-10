<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\xcoin\permissions;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;

/**
 * CreateEntry Permission
 */
class SubmitSpaceProjects extends \humhub\libs\BasePermission
{

    /**
     * @inheritdoc
     */
    protected $moduleId = 'xcoin';

    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_SUBMITTER
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [];

    public function getTitle()
    {
        return Yii::t('XcoinModule.base', 'Submit Project on behalf of this space');
    }

    public function getDescription()
    {
        return Yii::t('XcoinModule.base', 'Allows the user to submit project proposals on behalf of this space');
    }

}
