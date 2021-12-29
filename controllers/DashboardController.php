<?php
/**
 * Created By IJ
 * @author : Ala Daly <ala.daly@dotit-corp.com>
 * @date : 7‏/12‏/2021, Tue
 **/

namespace humhub\modules\xcoin\controllers;


use humhub\components\Controller;
use humhub\modules\xcoin\services\DashboardStatistics;
use Yii;
use yii\web\HttpException;

class DashboardController extends Controller
{


    public function getAccessRules()
    {
        return [
            ['login']
        ];
    }

    /**
     * @inheritdoc
     * @throws HttpException
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (!$this->module->isCrowdfundingEnabled()) {
            throw new HttpException(403, Yii::t('XcoinModule.base', 'Crowdfunding is not enabled'));
        }

        return true;
    }


    public function actionStatistics()
    {
        return $this->render('statistics', [
            'totalUsers'=>DashboardStatistics::getTotalUsers(),
            'totalTransactions'=>DashboardStatistics::getTotalOfTransactions(),
            'totalOffers'=>DashboardStatistics::getTotalOfMarketplaceOffers(),
            'totalMarketPlaces'=>DashboardStatistics::getTotalMarketplaces(),
        ]);
    }
}
