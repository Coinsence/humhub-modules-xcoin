<?php

use humhub\libs\Iso3166Codes;
use humhub\modules\xcoin\assets\Assets;
use \humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\helpers\ChallengeHelper;
use humhub\widgets\ActiveForm;
use kv4nt\owlcarousel\OwlCarouselWidget;
use humhub\assets\Select2BootstrapAsset;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use \yii\helpers\Url;
use \yii\helpers\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;
use humhub\modules\xcoin\models\Category;
use humhub\modules\xcoin\models\Challenge;


Assets::register($this);
Select2BootstrapAsset::register($this);

/** @var $selectedChallenge Challenge | null */
/** @var $fundings Funding[] */
/** @var $assetsList array */
/** @var $challengesList array */
/** @var $countriesList array */
/** @var $challengesCarousel array */

?>

<div class="crowd-funding">
    <div class="container">
        <div class="filters">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    OwlCarouselWidget::begin([
                        'container' => 'div',
                        'containerOptions' => [
                            'class' => 'categories'
                        ],
                        'pluginOptions' => [
                            'responsive' => [
                                0 => [
                                    'items' => 2
                                ],
                                520 => [
                                    'items' => 3
                                ],
                                768 => [
                                    'items' => 4
                                ],
                                1192 => [
                                    'items' => 5
                                ],
                                1366 => [
                                    'items' => 6
                                ],
                                1556 => [
                                    'items' => 8
                                ],
                            ],
                            'margin' => 10,
                            'nav' => true,
                            'dots' => false,
                            'navText' => ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>']
                        ]
                    ]);
                    ?>
                    <label class="category all">
                        <input type="radio" name="categroy" checked>
                        <a href="<?= Url::to(['/xcoin/funding-overview']) ?>"><span><label>All</label></span></a>
                    </label>
                    <?php foreach ($challengesCarousel as $challenge): ?>
                        <label class="category">
                            <a href="<?= Url::to(['/xcoin/funding-overview', 'challengeId' => $challenge['id']]) ?>">
                                <span style="background-image: url('<?= $challenge['img'] ?>'); "><label><?= $challenge['text'] ?></label></span>
                            </a>
                        </label>
                    <?php endforeach; ?>
                    <?php OwlCarouselWidget::end(); ?>
                </div>
            </div>
            <?php $form = ActiveForm::begin(['id' => 'filter-form']); ?>
            <div class="row">
                <?php if (!empty($assetsList)) : ?>
                    <div class="col-md-3 asset">
                        <?=
                        $form->field($model, 'asset_id')->widget(Select2::class, [
                            'data' => $assetsList,
                            'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Asset') . ' - ', 'value' => $model->asset_id ? $model->asset_id : []],
                            'theme' => Select2::THEME_BOOTSTRAP,
                            'hideSearch' => false,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                            ],
                        ])->label(false)
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($challengesList)) : ?>
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
                <?php endif; ?>
                <div class="col-md-3">
                    <?= $form->field($model, 'categories')->widget(Select2::class, [
                        'data' => ArrayHelper::map(Category::find()->where(['type' => Category::TYPE_FUNDING])->all(), 'id', 'name'),
                        'options' => [
                            'placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Categories') . ' - ',
                            'multiple' => true,
                        ],
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                        ],
                    ])->label(false); ?>
                </div>
                <div class="col-md-3 location">
                    <div id="location-field" class="location-field">
                        <div class="location-selection">
                            <span class="selection-text">
                                <?php if ($model->country): ?>
                                    <?= iso3166Codes::country($model->country) ?>
                                <?php endif ?>
                                <?php if ($model->city && $model->country): ?>
                                    <?= Html::encode(' , ' . ucfirst($model->city)) ?>
                                <?php else : ?>
                                    <?= Html::encode(ucfirst($model->city)) ?>
                                <?php endif ?>
                                <?php if (!$model->country && !$model->city): ?>
                                    <?= Yii::t('XcoinModule.funding', 'Select location..') ?>
                                <?php endif ?>
                            </span>
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
                                            'data' => iso3166Codes::$countries,
                                            'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Select country') . ' - '],
                                            'theme' => Select2::THEME_BOOTSTRAP,
                                            'hideSearch' => false,
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
                                <a class="reset-location" href="javascript:"><i class="fa fa-undo"></i> Reset</a>
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
                    <?= Html::Button(Yii::t('XcoinModule.funding', 'Reset'), ['class' => 'reset btn btn-default']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <div class="content">
            <div class="row header">
                <div class="col-md-6">
                    <span class="num-projects"><?= count($fundings) . ' ' . Yii::t('XcoinModule.funding', 'Project(s)') ?></span>
                </div>
            </div>
            <div class="panels">
                <?php if ($selectedChallenge): ?>
                    <div class="col-sm-6 col-md-4 col-lg-3">
                        <?php
                        $challengeCover = ChallengeHelper::getChallengeCoverUrl($selectedChallenge->id);
                        ?>
                        <a class="marketplace-card" href="<?= $selectedChallenge->space->createUrl('/xcoin/challenge/overview', ['challengeId' => $selectedChallenge->id]) ?>">
                            <span style="background-image: url('<?= $challengeCover ?>'); ">
                                <?php if ($challengeCover): ?>
                                    <?= Html::img($challengeCover) ?>    
                                <?php endif; ?>
                                <label>Go to <?= $selectedChallenge->title ?></label>
                            </span>
                        </a>
                    </div>
                <?php endif; ?>
                <?php foreach ($fundings as $funding): ?>
                    <?php if ($funding->getRemainingDays() > 0): ?>
                        <?php
                        $space = $funding->getSpace()->one();
                        $cover = $funding->getCover();
                        ?>
                        <a href="<?= $space->createUrl('/xcoin/funding/details', [
                            'fundingId' => $funding->id
                        ]); ?>"
                            data-target="#globalModal">
                            <div class="col-sm-6 col-md-4 col-lg-3">
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
                                            <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
                                                'height' => '140'
                                            ]) ?>
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
                                    <div class="panel-body <?= $funding->hidden_details && $funding->hidden_location ? 'sm' : ''?>">
                                        <h4 class="funding-title">
                                            <?= Html::encode($funding->title); ?>
                                            <?php if ($funding->review_status == Funding::FUNDING_NOT_REVIEWED) : ?>
                                                <div style="color: orange; display: inline">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                                       title="<?= Yii::t('XcoinModule.funding', 'Under review') ?>"></i>
                                                </div>
                                            <?php elseif ($funding->review_status == Funding::FUNDING_LAUNCHING_SOON) : ?>
                                                <div style="color: orange; display: inline">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                                       title="<?= Yii::t('XcoinModule.funding', 'Launching soon') ?>"></i>
                                                </div>
                                            <?php else: ?>
                                                <div style="color: dodgerblue; display: inline">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                                       title="<?= Yii::t('XcoinModule.funding', 'Verified') ?>"></i>
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

                                                <?php if (!$funding->hidden_location): ?>
                                                <!-- campaign location start -->
                                                <p class="funding-location"><i class="fa fa-map-marker"></i><?= Iso3166Codes::country($funding->country) . ', ' . $funding->city ?></p>
                                                <!-- campaign location end -->
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!$funding->hidden_details): ?>

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
                                                <?php if ($funding->review_status == Funding::FUNDING_LAUNCHING_SOON): ?>
                                                    <strong style="color: orange"><?= Yii::t('XcoinModule.funding', 'Launching soon') ?></strong>
                                                <?php else : ?>
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
                                                <?php endif; ?>
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
                                                    'space' => $funding->getChallenge()->one()->asset->space,
                                                    'width' => 16,
                                                    'showTooltip' => true,
                                                    'link' => false
                                                ]); ?>
                                                <!-- campaign requesting end -->
                                            </div>

                                        </div>

                                    </div>

                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
