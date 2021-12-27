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

    public static function getTotalUsers()
    {

        $users = User::find()->all();
        $dates = [];
        $values = [];
        $updatedProfiles = [];
        foreach ($users as $user) {
            array_push($dates, date('Y-m-d', strtotime($user->created_at)));
            array_push($values, (int)count(User::find()->where(['<=', 'created_at', $user->created_at])->all()));
            array_push($updatedProfiles, (int)count(User::find()->where(['<=', 'updated_at', $user->created_at])->all()));
        }
        $dataSet["dates"] = $dates;
        $dataSet["values"] = $values;
        $dataSet["updatedProfiles"] = $updatedProfiles;
        return $dataSet;
    }

    public static function getTotalOfTransactions()
    {

        $transactions = Transaction::find()->all();
        $dates = [];
        $values = [];
        $volumes = [];
        foreach ($transactions as $transaction) {
            array_push($dates, date('Y-m-d', strtotime($transaction->created_at)));
            $thatDayTransactions = Transaction::find()->where(['<=', 'created_at', $transaction->created_at])->all();
            $transactionsVolume = 0;
            foreach ($thatDayTransactions as $tr) {
                $transactionsVolume += $tr->amount;
            }
            array_push($values, (int)count($thatDayTransactions));
            array_push($volumes, $transactionsVolume);
        }
        $dataSet["dates"] = $dates;
        $dataSet["values"] = $values;
        $dataSet["volumes"] = $volumes;
        return $dataSet;
    }

    public static function getTotalOfMarketplaceOffers()
    {

        $products = Product::find()->all();
        $dates = [];
        $values = [];
        foreach ($products as $product) {
            array_push($dates, date('Y-m-d', strtotime($product->created_at)));
            array_push($values, (int)count(Product::find()->where(['<=', 'created_at', $product->created_at])->all()));
        }
        $dataSet["dates"] = $dates;
        $dataSet["values"] = $values;
        return $dataSet;
    }

    public static function getTotalOfFundings()
    {

        $fundings = Funding::find()->all();
        $dates = [];
        $values = [];
        foreach ($fundings as $funding) {
            array_push($dates, date('Y-m-d', strtotime($funding->created_at)));
            array_push($values, (int)count(Funding::find()->where(['<=', 'created_at', $funding->created_at])->all()));

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
    public static function getTotalMarketplaces()
    {
        $marketPlaces = Marketplace::find()->all();
        $dates = [];
        $values = [];
        foreach ($marketPlaces as $marketPlace) {
            array_push($dates, date('Y-m-d', strtotime($marketPlace->created_at)));
            array_push($values, (int)count(Funding::find()->where(['<=', 'created_at', $marketPlace->created_at])->all()));
        }
        $dataSet["dates"] = $dates;
        $dataSet["values"] = $values;
        return $dataSet;
    }
}
