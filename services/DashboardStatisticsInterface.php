<?php
namespace humhub\modules\xcoin\services;

interface DashboardStatisticsInterface
{
    public static function getTotalUsers($startDate = null ,$endDate = null , $type = null);

    public static function getTotalOfTransactions($startDate = null, $endDate = null, $type = null);

    public static function getTotalOfMarketplaceOffers($startDate = null, $endDate = null, $type = null);

    public static function getTotalOfFundings();

    public static function getCoinsPerUser();

    public static function getTotalSpaces();

    public static function getTotalMarketplaces($startDate = null, $endDate = null, $type = null);

    public static function getUserDistributionBasedOnLogin($startDate, $endDate);

}

?>
