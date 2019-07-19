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
class ReviewPublicOffers extends \humhub\libs\BasePermission
{

    /**
     * @inheritdoc
     */
    protected $moduleId = 'xcoin';

    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [];

    public function getTitle()
    {
        return Yii::t('XcoinModule.base', 'Review Public Offers');
    }

    public function getDescription()
    {
        return Yii::t('XcoinModule.base', 'Allows the user to review Crowdfunding campaigns & Products');
    }

}
