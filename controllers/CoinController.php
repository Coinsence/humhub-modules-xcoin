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
class CoinController extends ContentContainerController
{

    public function actionIndex()
    {
        if ($this->contentContainer instanceof Space) {
            return $this->render('index_space', [
                'asset' => AssetHelper::getSpaceAsset($this->contentContainer)
            ]);
        } else {
            return $this->render('userCoin', [
                'isOwner' => ($this->contentContainer->id === Yii::$app->user->id)
            ]);
        }
    }

   
  
}
