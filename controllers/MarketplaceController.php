<?php

namespace humhub\modules\xcoin\controllers;

use humhub\components\Controller;

class MarketplaceController extends Controller
{

    public function actionIndex()
    {
        return $this->render('index');
    }

}
