<?php

namespace humhub\modules\xcoin\controllers;

use humhub\modules\xcoin\models\Funding;
use humhub\components\Controller;

class FundingOverviewController extends Controller
{

    public function actionIndex()
    {
        $query = Funding::find();
        $query->where(['>', 'xcoin_funding.amount', 0]);
        $query->andWhere(['IS NOT', 'xcoin_funding.id', new \yii\db\Expression('NULL')]);
        
        return $this->render('index', ['fundings' => $query->all()]);
    }

}
