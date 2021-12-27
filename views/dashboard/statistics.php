<?php
/**
 * Created By IJ
 * @author : Ala Daly <ala.daly@dotit-corp.com>
 * @date : 7‏/12‏/2021, Tue
 **/

use dosamigos\chartjs\ChartJs;
use humhub\modules\xcoin\assets\Assets;
use humhub\assets\Select2BootstrapAsset;
use humhub\modules\xcoin\services\DashboardStatistics;

Assets::register($this);
Select2BootstrapAsset::register($this);
/* @var $totalUsers [] */
/* @var $totalTransactions [] */
/* @var $totalOffers [] */
/* @var $totalFundings [] */
/* @var $coinsPerUsers [] */
/* @var $totalSpaces [] */
/* @var $totalMarketPlaces [] */
?>

<div class="crowd-funding">
    <div class="container">
        <div class="filters">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <?= ChartJs::widget([
                            'type' => 'line',
                            'id' => 'totalUsers',
                            'options' => [
                                'height' => 400,
                                'width' => 400,
                            ],
                            'data' => [
                                'labels' => $totalUsers['dates'], // Your labels
                                'datasets' => [
                                    [
                                        'data' => $totalUsers['values'], // Your dataset
                                        'label' => 'Total of users',
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => false,
                                    'position' => 'bottom',
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062",
                                    ]
                                ],
                                'tooltips' => [
                                    'enabled' => true,
                                    'intersect' => true
                                ],
                                'hover' => [
                                    'mode' => false
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => Yii::t('XcoinModule.funding', 'User registration'),
                                ],
                                'maintainAspectRatio' => false,

                            ]
                        ])
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?= ChartJs::widget(['type' => 'line',
                            'id' => 'transactions',
                            'options' => ['height' => 400,
                                'width' => 400,],
                            'data' => [
                                'labels' => $totalTransactions['dates'], // Your labels
                                'datasets' => [
                                    [
                                        'data' => $totalTransactions['values'], // Your dataset
                                        'label' => '',
                                        'borderWidth' => 1,
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => false,
                                    'position' => 'bottom',
                                    'title' => [
                                        'display' => true,
                                        'text' => Yii::t('XcoinModule.funding', 'Custom shit'),
                                    ],
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062",
                                    ]
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => Yii::t('XcoinModule.funding', 'Number of transactions'),
                                ],
                                'tooltips' => [
                                    'enabled' => true,
                                    'intersect' => true
                                ],
                                'hover' => [
                                    'mode' => false
                                ],
                                'maintainAspectRatio' => false,
                            ]])
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?= ChartJs::widget([
                            'type' => 'line',
                            'id' => 'volumeOfTransactions',
                            'options' => [
                                'height' => 400,
                                'width' => 400,
                            ],
                            'data' => [
                                'labels' => $totalTransactions['dates'], // Your labels
                                'datasets' => [
                                    ['data' => $totalTransactions['volumes'], // Your dataset
                                        'label' => '',
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' =>
                                    ['display' => false,
                                        'position' => 'bottom',
                                        'labels' =>
                                            ['fontSize' => 14,
                                                'fontColor' => "#425062"
                                                ,]
                                    ],
                                'tooltips' => [
                                    'enabled' => true,
                                    'intersect' => true
                                ],
                                'hover' => [
                                    'mode' => false
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => Yii::t('XcoinModule.funding', 'Volume of transactions'),
                                ],
                                'maintainAspectRatio' => false,
                            ]
                        ])
                        ?>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <?= ChartJs::widget([
                            'type' => 'line',
                            'id' => 'totalOffers',
                            'options' => [
                                'height' => 400,
                                'width' => 400,],
                            'data' => [
                                'labels' => $totalOffers['dates'], // Your labels
                                'datasets' => [
                                    [
                                        'data' => $totalOffers['values'], // Your dataset
                                        'label' => '',
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => false,
                                    'position' => 'bottom',
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062"
                                        ,]
                                ],
                                'tooltips' => [
                                    'enabled' => true,
                                    'intersect' => true
                                ],
                                'hover' => [
                                    'mode' => false
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => Yii::t('XcoinModule.funding', 'Total of products'),
                                ],
                                'maintainAspectRatio' => false,]])
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?= ChartJs::widget(['type' => 'line',
                            'id' => 'totalfundings',
                            'options' => [
                                'height' => 400,
                                'width' => 400
                                ,],
                            'data' => ['labels' => $totalFundings['dates'], // Your labels
                                'datasets' => [
                                    [
                                        'data' => $totalFundings['values'], // Your dataset
                                        'label' => Yii::t('XcoinModule.funding', 'Total of fundings'),
                                    ]
                                ]
                            ],
                            'clientOptions' =>
                                [
                                    'legend' => [
                                        'display' => false,
                                        'position' => 'bottom',
                                    ],
                                    'tooltips' =>
                                        [
                                            'enabled' => true,
                                            'intersect' => true
                                        ],
                                    'title' =>
                                        [
                                            'display' => true,
                                            'text' => Yii::t('XcoinModule.funding', 'Total of fundings'),
                                        ],
                                    'hover' => [
                                        'mode' => false],
                                    'maintainAspectRatio' => false,
                                ]])
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?= ChartJs::widget(['type' => 'line',
                            'id' => 'totalUpdatedProfiles',
                            'options' => [
                                'height' => 400,
                                'width' => 400,
                            ],
                            'data' => [
                                'labels' => $totalUsers['dates'], // Your labels
                                'datasets' => [
                                    [
                                        'data' => $totalUsers['updatedProfiles'], // Your dataset
                                        'label' => 'Total of updated profiles'
                                        ,
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => false,
                                    'position' => 'bottom',
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062",]
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => Yii::t('XcoinModule.funding', 'Total of updated profiles'),
                                ],
                                'tooltips' => [
                                    'enabled' => true,
                                    'intersect' => true],
                                'hover' => [
                                    'mode' => false
                                ],
                                'maintainAspectRatio' => false,
                            ]
                        ])
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-4">
                        <?= ChartJs::widget(['type' => 'bar',
                            'id' => 'coinsPerUsers',
                            'options' => [
                                'height' => 400,
                                'width' => 400,
                            ],
                            'data' => [
                                'labels' => $coinsPerUsers['names'], // Your labels
                                'datasets' => [
                                    [
                                        'data' => $coinsPerUsers['values'], // Your dataset
                                        'label' => 'Total of Coin per user '
                                        ,
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => true,
                                    'position' => 'bottom',
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062",]
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => Yii::t('XcoinModule.funding', 'Coins per user'),
                                ],
                                'tooltips' => [
                                    'enabled' => true,
                                    'intersect' => true],
                                'hover' => [
                                    'mode' => false
                                ],
                                'maintainAspectRatio' => false,
                            ]
                        ])
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?= ChartJs::widget(['type' => 'line',
                            'id' => 'totalSpaces',
                            'options' => [
                                'height' => 400,
                                'width' => 400,
                            ],
                            'data' => [
                                'labels' => $totalSpaces['dates'], // Your labels
                                'datasets' => [
                                    [
                                        'data' => $totalSpaces['values'], // Your dataset
                                        'label' => 'Total of Spaces '
                                        ,
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => false,
                                    'position' => 'bottom',
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062",]
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => Yii::t('XcoinModule.funding', 'Total of spaces'),
                                ],
                                'tooltips' => [
                                    'enabled' => true,
                                    'intersect' => true
                                ],
                                'hover' => [
                                    'mode' => false
                                ],
                                'maintainAspectRatio' => false,
                            ]
                        ])
                        ?>
                    </div>
                    <div class="col-md-4">
                        <?= ChartJs::widget(['type' => 'line',
                            'id' => 'totalMarketPlaces',
                            'options' => [
                                'height' => 400,
                                'width' => 400,
                            ],
                            'data' => [
                                'labels' => $totalMarketPlaces['dates'], // Your labels
                                'datasets' => [
                                    [
                                        'data' => $totalMarketPlaces['values'], // Your dataset
                                        'label' => 'Total of market places '
                                        ,
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => false,
                                    'position' => 'bottom',
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062",]
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => Yii::t('XcoinModule.funding', 'Total of market places'),
                                ],
                                'tooltips' => [
                                    'enabled' => true,
                                    'intersect' => true],
                                'hover' => [
                                    'mode' => false
                                ],
                                'maintainAspectRatio' => false,
                            ]
                        ])
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= ChartJs::widget([
                            'type' => 'doughnut',
                            'id' => 'userDistribution',
                            'options' => [
                                'height' => 400,
                                'width' => 400,
                            ],
                            'data' => [
                                'labels' => [
                                    Yii::t('XcoinModule.funding', 'Below 24h'),
                                    Yii::t('XcoinModule.funding', 'Between 1 day and 7 days'),
                                    Yii::t('XcoinModule.funding', 'Between 7 days and 30 days'),
                                    Yii::t('XcoinModule.funding', 'Between 30 days and 90 days'),
                                    Yii::t('XcoinModule.funding', 'Between 90 days and 365 days'),
                                    Yii::t('XcoinModule.funding', 'More then 1 year'),
                                ],
                                'datasets' => [
                                    [
                                        'data' => [
                                            DashboardStatistics::getUserDistributionBasedOnLogin(1, 0),
                                            DashboardStatistics::getUserDistributionBasedOnLogin(7, 0),
                                            DashboardStatistics::getUserDistributionBasedOnLogin(30, 7),
                                            DashboardStatistics::getUserDistributionBasedOnLogin(90, 30),
                                            DashboardStatistics::getUserDistributionBasedOnLogin(365, 90),
                                            DashboardStatistics::getUserDistributionBasedOnLogin(0, 365),
                                        ], // Your dataset
                                        'backgroundColor' => ['green', 'Brown', 'DarkMagenta', 'GreenYellow', 'Violet', 'DodgerBlue'],
                                        'hoverOffset' => 4
                                    ]
                                ]
                            ],
                            'clientOptions' => [
                                'legend' => [
                                    'display' => false,
                                    'position' => 'left',
                                    'labels' => [
                                        'fontSize' => 14,
                                        'fontColor' => "#425062",
                                    ]
                                ],
                                'tooltips' => [
                                    'enabled' => true,
                                    'intersect' => true
                                ],
                                'title' => [
                                    'display' => true,
                                    'text' => Yii::t('XcoinModule.funding', 'User distribution based on time since last login'),
                                ],

                                'hover' => [
                                    'mode' => false
                                ],
                                'maintainAspectRatio' => false,
                            ]
                        ])
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
