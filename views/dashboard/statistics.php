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
/* @var $startDate string */
/* @var $endDate string */
/* @var $type string */
?>

<div class="dashboard">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?= Yii::t('XcoinModule.stats', 'Statistics: ') ?></h1>
            </div>
            <div class="col-md-2">
                <div class="filter col-md-12">
                    <label for="startDate"><?= Yii::t('XcoinModule.stats', 'Start date: ') ?></label>
                    <input type="date" value="<?= $startDate ?>" id="startDate">

                    <label for="endDate"><?= Yii::t('XcoinModule.stats', 'End date: ') ?></label>
                    <input type="date" value="<?= $endDate ?>" id="endDate">

                    <label for="type-select"><?= Yii::t('XcoinModule.stats', 'Resolution:') ?></label>
                    <select name="type" id="type-select">
                        <option value="" disabled>--Please choose an option--</option>
                        <option value="monthly"<?= $type == "monthly" ? "selected" : "" ?> ><?= Yii::t('XcoinModule.stats', 'Monthly') ?></option>
                        <option value="daily" <?= $type == "daily" ? "selected" : "" ?>><?= Yii::t('XcoinModule.stats', 'Daily') ?></option>
                        <option value="weekly" <?= $type == "weekly" ? "selected" : "" ?>><?= Yii::t('XcoinModule.stats', 'Weekly') ?></option>
                    </select>

                    <button type="button" class="btn btn-primary"
                            onclick="updateData()"> <?= Yii::t('XcoinModule.stats', 'Show data') ?>
                    </button>
                </div>                
            </div>
            <div class="col-md-10">
                <div class="stats col-md-12">
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
                                            'text' => Yii::t('XcoinModule.stats', 'User registration'),
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
                                                'text' => Yii::t('XcoinModule.stats', 'Custom shit'),
                                            ],
                                            'labels' => [
                                                'fontSize' => 14,
                                                'fontColor' => "#425062",
                                            ]
                                        ],
                                        'title' => [
                                            'display' => true,
                                            'text' => Yii::t('XcoinModule.stats', 'Number of transactions'),
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
                                            'text' => Yii::t('XcoinModule.stats', 'Total of products'),
                                        ],
                                        'maintainAspectRatio' => false,]])
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
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
                                            'text' => Yii::t('XcoinModule.stats', 'Total of updated profiles'),
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
                                            'text' => Yii::t('XcoinModule.stats', 'Total of market places'),
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
                                <?= ChartJs::widget([
                                    'type' => 'bar',
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
                                            'text' => Yii::t('XcoinModule.stats', 'User distribution based on time since last login'),
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
       
    </div>
</div>

<script>
    function updateData() {
        startDate = $('#startDate').val();
        endDate = $('#endDate').val();
        type = $('#type-select').val();
        if (startDate && endDate && endDate > startDate) {
            location.href = "/xcoin/dashboard/statistics?startDate=" + startDate + "&endDate=" + endDate + "&type=" + type;
        }

    }
</script>
