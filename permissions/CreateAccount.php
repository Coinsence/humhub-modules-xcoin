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
class CreateAccount extends \humhub\libs\BasePermission
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
        Space::USERGROUP_SUBMITTER,
        Space::USERGROUP_MEMBER,
        User::USERGROUP_SELF
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        User::USERGROUP_FRIEND,
        User::USERGROUP_GUEST,
        User::USERGROUP_USER,
        User::USERGROUP_FRIEND,
    ];

    public function getTitle()
    {
        return Yii::t('XcoinModule.base', 'Create account');
    }

    public function getDescription()
    {
        return Yii::t('XcoinModule.base', 'Allows the user to create an own account');
    }

}
