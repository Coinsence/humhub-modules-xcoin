<?php

namespace humhub\modules\xcoin;

use humhub\modules\xcoin\permissions\CreateAccount;
use humhub\modules\xcoin\permissions\ReviewSubmittedProjects;
use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\content\components\ContentContainerActiveRecord;

class Module extends ContentContainerModule
{

    public $resourcesPath = 'resources';

    /**
     * @inheritdoc
     */
    public function getContentContainerTypes()
    {
        return [
            Space::class,
            User::class
        ];
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerName(ContentContainerActiveRecord $container)
    {
        return Yii::t('XcoinModule.base', 'Accounting');
    }

    /**
     * @inheritdoc
     */
    public function getContentContainerDescription(ContentContainerActiveRecord $container)
    {
        return Yii::t('XcoinModule.base', 'Accounting system for projects');
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($contentContainer = null)
    {
        $permissions [] = new permissions\ReviewSubmittedProjects();

        if ($contentContainer !== null) {
            $permissions [] = new permissions\CreateAccount();
        }

        $permissions [] = new permissions\SubmitSpaceProjects();

        return $permissions;
    }

    public function getContentContainerConfigUrl(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            return $container->createUrl('/xcoin/config/index');
        }
    }

}
