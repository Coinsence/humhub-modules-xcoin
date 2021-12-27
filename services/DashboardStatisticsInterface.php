<?php
namespace humhub\modules\xcoin\services;

interface DashboardStatisticsInterface
{
    public static function getTotalUsers();

    public static function getTotalOfTransactions();

    public static function getTotalOfMarketplaceOffers();

    public static function getTotalOfFundings();

    public static function getCoinsPerUser();

    public static function getTotalSpaces();

    public static function getTotalMarketplaces();

    public static function getUserDistributionBasedOnLogin($startDate, $endDate);

}

?>
