<?php

namespace humhub\modules\xcoin\controllers;

use humhub\components\Event;
use Yii;
use humhub\modules\content\components\ContentContainerController;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AssetHelper;

/**
 * Description of AccountController
 *
 * @author Luke
 * @author gdaly
 */
class OverviewController extends ContentContainerController
{
    const EVENT_SPACE_INDEX = 'spaceIndex';

    public function actionIndex()
    {
        AssetHelper::initContentContainer($this->contentContainer);
        AccountHelper::initContentContainer($this->contentContainer);

        Event::trigger(OverviewController::class, OverviewController::EVENT_SPACE_INDEX, new Event(['sender' => $this->contentContainer]));

        if ($this->contentContainer instanceof Space) {

            return $this->render('index_space', [
                'asset' => AssetHelper::getSpaceAsset($this->contentContainer)
            ]);
        } else {
            return $this->render('index_profile', [
                'isOwner' => ($this->contentContainer->id === Yii::$app->user->id)
            ]);
        }
    }

    public function actionLatestTransactions()
    {
        return $this->render('latest-transactions');
    }

    public function actionLatestAssetTransactions()
    {
        return $this->render('latest-asset-transactions', ['asset' => AssetHelper::getSpaceAsset($this->contentContainer)]);
    }

    public function actionShareholderList()
    {
        return $this->render('shareholder-list', ['asset' => AssetHelper::getSpaceAsset($this->contentContainer)]);
    }
}
