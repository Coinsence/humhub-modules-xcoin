<?php

namespace humhub\modules\xcoin\services;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\Marketplace;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\models\Transaction;
use humhub\modules\xcoin\services\DashboardStatisticsInterface;

/**
 * Created By IJ
 * @author : Ala Daly <ala.daly@dotit-corp.com>
 * @date : 9‏/12‏/2021, Thu
 **/
Class DashboardStatistics implements DashboardStatisticsInterface
{

    public static function getTotalUsers($startDate = null, $endDate = null, $type = null)
    {
        $dates = [];
        $values = [];
        $updatedProfiles = [];
        $daysToAdd = self::getDaysToAdd($type);
        if ($startDate == null || $endDate == null) {
            $startDate = date("Y-m-d", strtotime("-1 year", time()));
            $endDate = date("Y-m-d", time());
        }
        while ($endDate > $startDate) {
            array_push($dates, $startDate);
            array_push($values, User::find()->where(['<=', 'created_at', $startDate])->count());
            array_push($updatedProfiles, User::find()->where(['<=', 'updated_at', $startDate])->count());
            $startDate = date('Y-m-d', strtotime($startDate . ' +' . $daysToAdd . 'days'));
        }
        $dataSet["dates"] = $dates;
        $dataSet["values"] = $values;
        $dataSet["updatedProfiles"] = $updatedProfiles;
        return $dataSet;
    }

    public static function getTotalOfTransactions($startDate = null, $endDate = null, $type = null)
    {
        $dates = [];
        $values = [];
        $volumes = [];
        $daysToAdd = self::getDaysToAdd($type);
        if ($startDate == null || $endDate == null) {
            $startDate = date("Y-m-d", strtotime("-1 year", time()));
            $endDate = date("Y-m-d", time());
        }
        while ($endDate > $startDate) {
            array_push($dates, $startDate);
            array_push($values, Transaction::find()->where(['<=', 'created_at', $startDate])->count());
            $startDate = date('Y-m-d', strtotime($startDate . ' +' . $daysToAdd . 'days'));

        }
        $dataSet["dates"] = $dates;
        $dataSet["values"] = $values;
//        $dataSet["volumes"] = $volumes;
        return $dataSet;
    }

    public static function getTotalOfMarketplaceOffers($startDate = null, $endDate = null, $type = null)
    {

        $dates = [];
        $values = [];
        $daysToAdd = self::getDaysToAdd($type);
        if ($startDate == null || $endDate == null) {
            $startDate = date("Y-m-d", strtotime("-1 year", time()));
            $endDate = date("Y-m-d", time());
        }
        while ($endDate > $startDate) {
            array_push($dates, $startDate);
            array_push($values, Product::find()->where(['<=', 'created_at', $startDate])->count());
            $startDate = date('Y-m-d', strtotime($startDate . ' +' . $daysToAdd . 'days'));

        }

        $dataSet["dates"] = $dates;
        $dataSet["values"] = $values;
        return $dataSet;
    }

    public static function getTotalOfFundings($startDate = null, $endDate = null, $type = null)
    {
        $dates = [];
        $values = [];
        $daysToAdd = self::getDaysToAdd($type);
        if ($startDate == null || $endDate == null) {
            $startDate = date("Y-m-d", strtotime("-1 year", time()));
            $endDate = date("Y-m-d", time());
        }
        while ($endDate > $startDate) {
            array_push($dates, $startDate);
            array_push($values, Funding::find()->where(['<=', 'created_at', $startDate])->count());
            $startDate = date('Y-m-d', strtotime($startDate . ' +' . $daysToAdd . 'days'));

        }
        $dataSet["dates"] = $dates;
        $dataSet["values"] = $values;
        return $dataSet;
    }

    public static function getUserDistributionBasedOnLogin($startDate, $endDate)
    {
        $today = new \DateTime('now');
        $endDate = date('Y-m-d H:i', strtotime('-' . $endDate . 'day', strtotime($today->format('Y-m-d H:i'))));
        $startDate = date('Y-m-d H:i', strtotime('-' . $startDate . 'day', strtotime($today->format('Y-m-d H:i'))));
        return count(User::find()
            ->where(['between', 'last_login', $startDate, $endDate])->all());
    }

    public static function getCoinsPerUser()
    {
        $users = User::find()->all();
        $values = [];
        $names = [];
        foreach ($users as $user) {
            $balance = 0;
            $accounts = Account::find()->where(['user_id' => $user->id])->all();
            foreach ($accounts as $account) {
                foreach ($account->getAssets() as $asset) {
                    $balance += $account->getAssetBalance($asset);
                }
            }
            if ($balance < 0) {
                continue;
            }
            array_push($names, $user->username);
            array_push($values, $balance);
        }
        $dataSet["names"] = $names;
        $dataSet["values"] = $values;
        return $dataSet;
    }

    public static function getTotalSpaces()
    {
        $spaces = Space::find()->all();
        $dates = [];
        $values = [];
        foreach ($spaces as $space) {
            array_push($dates, date('Y-m-d', strtotime($space->created_at)));
            array_push($values, (int)count(Funding::find()->where(['<=', 'created_at', $space->created_at])->all()));
        }
        $dataSet["dates"] = $dates;
        $dataSet["values"] = $values;
        return $dataSet;
    }

    public static function getTotalMarketplaces($startDate = null, $endDate = null, $type = null)
    {
        $marketPlaces = Marketplace::find()->all();
        $dates = [];
        $values = [];
        $daysToAdd = self::getDaysToAdd($type);
        if ($startDate == null || $endDate == null) {
            $startDate = date("Y-m-d", strtotime("-1 year", time()));
            $endDate = date("Y-m-d", time());
        }
        while ($endDate > $startDate) {
            array_push($dates, $startDate);
            array_push($values, Marketplace::find()->where(['<=', 'created_at', $startDate])->count());
            $startDate = date('Y-m-d', strtotime($startDate . ' +' . $daysToAdd . 'days'));

        }
        $dataSet["dates"] = $dates;
        $dataSet["values"] = $values;
        return $dataSet;
    }

    public static function getDaysToAdd($type)
    {
        switch ($type) {
            case "weekly":
                return 7;
                break;
            case "daily":
                return 1;
                break;
            case "monthly":
                return 30;
                break;
        }
        return 30;
    }
}
