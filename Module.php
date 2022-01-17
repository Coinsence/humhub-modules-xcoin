<?php

namespace humhub\modules\xcoin;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\content\components\ContentContainerModule;
use humhub\modules\content\components\ContentContainerActiveRecord;
use yii\helpers\Url;

class Module extends ContentContainerModule
{

    public $resourcesPath = 'resources';

    /**
     * @inheritdoc
     */
    public function getConfigUrl()
    {
        return Url::to(['/xcoin/admin/config']);
    }

    /**
     * Determines crowdfunding feature is enabled or not
     *
     * @return boolean
     */
    public function isCrowdfundingEnabled()
    {
        return $this->settings->get('isCrowdfundingEnabled', true);
    }

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
        $permissions [] = new permissions\IssueCoins();
        $permissions [] = new permissions\SubmitSpaceProjects();

        if ($contentContainer !== null) {
            $permissions [] = new permissions\CreateAccount();
        }

        return $permissions;
    }

    public function getContentContainerConfigUrl(ContentContainerActiveRecord $container)
    {
        if ($container instanceof Space) {
            return $container->createUrl('/xcoin/config/index');
        }
    }
}
