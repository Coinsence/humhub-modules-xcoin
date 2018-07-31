<?php

namespace humhub\modules\xcoin\controllers;

use Yii;
use humhub\components\Controller;
use humhub\modules\space\models\Space;

class FundingOverviewController extends Controller
{

    public function actionIndex()
    {
        $query = Space::find();
        $query->leftJoin('xcoin_funding', 'xcoin_funding.space_id=space.id AND xcoin_funding.available_amount != 0');
        $query->andWhere(['IS NOT', 'xcoin_funding.id', new \yii\db\Expression('NULL')]);
        
        return $this->render('index', ['spaces' => $query->all()]);
    }

}
