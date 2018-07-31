<?php

namespace humhub\modules\xcoin\controllers;

use Yii;
use humhub\components\Controller;
use humhub\modules\xcoin\models\Account;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Exchange;

class MarketplaceController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }

}
