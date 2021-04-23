<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\permissions;

use humhub\libs\BasePermission;
use humhub\modules\space\models\Space;
use Yii;

/**
 * SubmitSpaceProjects
 */
class SubmitSpaceProjects extends BasePermission
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
