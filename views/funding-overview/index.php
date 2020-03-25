<?php

use humhub\modules\xcoin\assets\Assets;
use \humhub\modules\xcoin\models\Funding;
use humhub\widgets\ActiveForm;
use kv4nt\owlcarousel\OwlCarouselWidget;
use humhub\assets\Select2BootstrapAsset;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use \yii\helpers\Url;
use \yii\helpers\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Progress;


/** @var $fundings Funding[] */
/** @var $spacesList array */
/** @var $challengesList array */
/** @var $countriesList array */

Assets::register($this);
Select2BootstrapAsset::register($this);


$img_placeholder = 'https://via.placeholder.com/600x400.png';
$categories = [
    [
        'id'    => 1,
        'text'  => 'Arts and Culture',
        'img'   => $img_placeholder
    ],
    [
        'id'    => 2,
        'text'  => 'Quality education and awareness creation',
        'img'   => $img_placeholder
    ],
    [
        'id'    => 3,
        'text'  => 'Child protection and youth empowerment',
        'img'   => $img_placeholder
    ],
    [
        'id'    => 4,
        'text'  => 'Community building and cohesion',
        'img'   => $img_placeholder
    ],
];

?>

<div class="crowd-funding">
    <div class="filters">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    OwlCarouselWidget::begin([
                        'container' => 'div',
                        'containerOptions' => [
                            'class' => 'categories'
                        ],
                        'pluginOptions'     => [
                            'responsive' => [
                                0       => [
                                    'items' => 2
                                ],
                                520     => [
                                    'items' => 3
                                ],
                                768     => [
                                    'items' => 4
                                ],
                                1192    => [
                                    'items' => 5
                                ],
                                1366    => [
                                    'items' => 6
                                ],
                                1556    => [
                                    'items' => 8
                                ],
                            ],
                            'margin'        => 10,
                            'nav'           => true,
                            'dots'          => false,
                            'navText'       => ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>']
                        ]
                    ]);
                    ?>
                    <label class="category all">
                        <input type="radio" name="categroy" checked>
                        <a href="<?= Url::to(['/xcoin/funding-overview']) ?>"><span>All</span></a>
                    </label>
                    <?php foreach ($categories as $category): ?>
                        <label class="category">
                            <input type="radio" name="categroy" value="<?= $category['id'] ?>">
                            <a href="<?= Url::to(['/xcoin/funding-overview', 'category' => $category['id']]) ?>"><span style="background-image: url('<?= $category['img'] ?>'); "><?= $category['text'] ?></span></a>
                        </label>
                    <?php endforeach; ?>
                    <?php OwlCarouselWidget::end(); ?>
                </div>
            </div>
            <?php $form = ActiveForm::begin(['id' => 'filter-form']); ?>
            <div class="row">
                <div class="col-md-3 space">
                    <?=
                    $form->field($model, 'space_id')->widget(Select2::class, [
                        'data' => $spacesList,
                        'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Space') . ' - ', 'value' => []],
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                        ],
                    ])->label(false)
                    ?>
                </div>
                <div class="col-md-3 challenge">
                    <?=
                    $form->field($model, 'challenge_id')->widget(Select2::class, [
                        'data' => $challengesList,
                        'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Challenge') . ' - ', 'value' => []],
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                        ],
                    ])->label(false)
                    ?>
                </div>
                <div class="col-md-3 location">
                    <div id="location-field" class="location-field">
                        <div class="location-selection">
                            <span class="selection-text">test, tt</span>
                            <span class="selection-arrow">
                                <b></b>
                            </span>
                        </div>
                        <div class="location-dropdown">
                            <div class="dropdown-body">
                                <div class="row">
                                    <div class="col-md-6"><?= Yii::t('XcoinModule.funding', 'Country:') ?></div>
                                    <div class="col-md-6">
                                        <?=
                                        $form->field($model, 'country')->widget(Select2::class, [
                                            'data' => $countriesList,
                                            'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Select a Country') . ' - ', 'value' => []],
                                            'theme' => Select2::THEME_BOOTSTRAP,
                                            'hideSearch' => true,
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                                            ],
                                        ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6"><?= Yii::t('XcoinModule.funding', 'City:') ?></div>
                                    <div class="col-md-6">
                                        <?=
                                        $form->field($model, 'city')->textInput([
                                            'placeholder' => Yii::t('XcoinModule.funding', 'Type your city name')
                                        ])->label(false)
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-footer">
                                <a class="reset-location" href="javascript:;"><i class="fa fa-undo"></i> Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 keywords">
                    <?=
                    $form->field($model, 'keywords')->textInput([
                        'placeholder' => Yii::t('XcoinModule.funding', 'Search by keyword..')
                    ])->label(false)
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 filter-actions">
                    <?= Html::submitButton(Yii::t('XcoinModule.funding', 'Apply filter'), ['class' => 'sumbit btn btn-gradient-1']) ?>
                    <?= Html::resetButton(Yii::t('XcoinModule.funding', 'Reset'), ['class' => 'reset btn btn-default']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <div class="row header">
                <div class="col-md-6">
                    <span class="num-projects"><?= count($fundings) . ' ' . Yii::t('XcoinModule.funding', 'Project(s)') ?></span>
                </div>
            </div>
            <div class="row panels">
                <div class="col-md-3">

                    <a href="<?= Url::to(['/xcoin/funding-overview/new']) ?>" data-target="#globalModal" class="add-project">
                        <span class="icon">
                            <i class="cross"></i>
                        </span>
                        <span class="text"><?= Yii::t('XcoinModule.funding', 'Create Your Project!') ?></span>
                    </a>
                </div>
                <?php foreach ($fundings as $funding): ?>
                    <?php if ($funding->getRemainingDays() > 0): ?>
                        <?php
                        $space = $funding->getSpace()->one();
                        $cover = $funding->getCover();
                        ?>
                        <a href="<?= $space->createUrl('/xcoin/funding/overview', [
                            'fundingId' => $funding->id
                        ]); ?>">
                            <div class="col-md-3">
                                <div class="panel">
                                    <div class="panel-heading">

                                        <!-- campaign cover start -->
                                        <?php if ($cover) : ?>
                                            <div class="bg"
                                                 style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                                            <?= Html::img($cover->getUrl(), ['height' => '140']) ?>
                                        <?php else : ?>
                                            <div class="bg"
                                                 style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>')"></div>
                                            <img src="<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>"
                                                 height="140"/>
                                        <?php endif ?>
                                        <!-- campaign cover end -->

                                        <div class="project-owner">

                                            <!-- space image start -->
                                            <?= SpaceImage::widget([
                                                'space' => $space,
                                                'width' => 34,
                                                'showTooltip' => true,
                                                'link' => false
                                            ]); ?>
                                            <!-- space image end -->

                                            <!-- campaign title start -->
                                            <span><?= Yii::t('XcoinModule.funding', 'Project by') . " <strong>" . Html::encode($space->name) . "</strong>"; ?></span>
                                            <!-- campaign title end -->

                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <h4 class="funding-title">
                                            <?= Html::encode($funding->title); ?>
                                            <?php if ($funding->review_status == Funding::FUNDING_NOT_REVIEWED) : ?>
                                                <div style="color: orange; display: inline">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.funding', 'Under review') ?>"></i>
                                                </div>
                                            <?php else: ?>
                                                <div style="color: dodgerblue; display: inline">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.funding', 'Verified') ?>"></i>
                                                </div>
                                            <?php endif; ?>
                                        </h4>
                                        <div class="media">
                                            <div class="media-left media-middle">
                                            </div>
                                            <div class="media-body">
                                                <!-- campaign description start -->
                                                <p class="media-heading"><?= Html::encode($funding->shortenDescription()); ?></p>
                                                <!-- campaign description end -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer">

                                        <div class="funding-progress">

                                            <div>
                                                <!-- campaign raised start -->
                                                <?= Yii::t('XcoinModule.funding', 'Raised:') ?>
                                                <strong><?= $funding->getRaisedAmount() ?> </strong>
                                                (<strong><?= $funding->getRaisedPercentage() ?>%</strong>)
                                                <!-- campaign raised end -->
                                            </div>

                                            <div class="pull-right">

                                                <!-- campaign remaining days start -->
                                                <?php if ($funding->getRemainingDays() > 2) : ?>
                                                    <div class="clock"></div>
                                                <?php else: ?>
                                                    <div class="clock red"></div>
                                                <?php endif; ?>
                                                <div class="days">
                                                    <strong><?= $funding->getRemainingDays() ?></strong> <?= $funding->getRemainingDays() > 1 ? Yii::t('XcoinModule.funding', 'Days left') : Yii::t('XcoinModule.funding', 'Day left') ?>
                                                </div>
                                                <!-- campaign remaining days end -->

                                            </div>

                                            <!-- campaign raised start -->
                                            <?php echo Progress::widget([
                                                'percent' => $funding->getRaisedPercentage(),
                                            ]); ?>
                                        </div>
                                        <div class="funding-details row">

                                            <div class="col-md-12">
                                                <!-- campaign requesting start -->
                                                <span class="text">
                                                    <?= Yii::t('XcoinModule.funding', 'Requesting:') ?>
                                                    <strong><?= $funding->getRequestedAmount() ?></strong>
                                                </span>
                                                <?= SpaceImage::widget([
                                                    'space' => $funding->asset->space,
                                                    'width' => 16,
                                                    'showTooltip' => true,
                                                    'link' => false
                                                ]); ?>
                                                <!-- campaign requesting end -->
                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
