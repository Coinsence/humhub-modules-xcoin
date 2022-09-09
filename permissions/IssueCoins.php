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
class IssueCoins extends \humhub\libs\BasePermission
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
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [];

    public function getTitle()
    {
        return Yii::t('XcoinModule.base', 'Issue coins');
    }

    public function getDescription()
    {
        return Yii::t('XcoinModule.base', 'Allows the user to issue coins');
    }

}
