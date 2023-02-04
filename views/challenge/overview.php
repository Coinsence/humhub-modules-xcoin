<?php

use humhub\libs\Iso3166Codes;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\FundingCategory;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\widgets\SocialShare;
use yii\bootstrap\Progress;

Assets::register($this);
Select2BootstrapAsset::register($this);

/** @var $challenge Challenge */
/** @var $fundings Funding[] */
/** @var $categories FundingCategory[] */
/** @var $activeCategory number */

?>

<div class="cs-overview">
    <?= Html::a('<i class="fa fa-long-arrow-left"></i>&nbsp;&nbsp;', ['/xcoin/challenge', 'container' => $this->context->contentContainer], ['class' => 'close-cs-overview']); ?>

    <?php
    $space = $challenge->getSpace()->one();
    $cover = $challenge->getCover();
    ?>

    <?php if ($challenge->hidden_description === Challenge::CHALLENGE_DESCRIPTION_HIDDEN): ?>
       <!-- campaign edit button start -->
       <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
            <?= Html::a(Yii::t('XcoinModule.challenge', 'Edit'), ['/xcoin/challenge/edit', 'id' => $challenge->id, 'container' => $this->context->contentContainer], ['data-target' => '#globalModal', 'class' => 'edit-btn']) ?>
        <?php endif; ?>
        <!-- campaign edit button end --> 
    <?php else: ?>
        <div class="panel panel-default panel-head">
            <div class="cs-overview-info">
                <h2 class="cs-overview-title"><?= $challenge->title ?></h2>
                <div class="cs-overview-asset">
                    <!-- challenge asset start -->
                    <?= SpaceImage::widget([
                        'space' => $challenge->asset->space,
                        'width' => 26,
                        'showTooltip' => true,
                        'link' => true
                    ]); ?>
                    <span class="asset-name"><?= $challenge->asset->space->name ?></span>
                    <!-- challenge asset end -->
                </div>
                <div class="cs-overview-description"><?= RichText::output($challenge->description); ?></div>
                <?php if ($challenge->isStopped()) : ?>
                    <div class="add-btn" style="color: red">
                        <?= Yii::t('XcoinModule.challenge', 'This challenge is stopped') ?>
                    </div>
                <?php elseif ($challenge->isClosed()): ?>
                    <div class="add-btn" style="color: red">
                        <?= Yii::t('XcoinModule.challenge', 'This challenge is closed') ?>
                    </div>
                <?php else: ?>
                    <h5 class="add-btn-text"><?= Yii::t('XcoinModule.challenge', 'If you have a project and want to join this campaign') ?></h5>
                    <?php if (Yii::$app->user->isGuest): ?>
                        <?= Html::a('<i class="fa fa-plus-circle"></i>&nbsp;&nbsp;' . Yii::t('XcoinModule.challenge', 'Add Your Project'), Yii::$app->user->loginUrl, ['class' => 'btn btn-gradient-1 add-btn', 'data-target' => '#globalModal']) ?>
                    <?php else: ?>
                        <?= Html::a('<i class="fa fa-plus-circle"></i>&nbsp;&nbsp;' . Yii::t('XcoinModule.challenge', 'Add Your Project'), [
                            '/xcoin/funding/new',
                            'challengeId' => $challenge->id,
                            'container' => $this->context->contentContainer
                        ], ['class' => 'btn btn-gradient-1 add-btn', 'data-target' => '#globalModal']); ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <div class="img-container">
                <!-- challenge image start -->
                <?php if ($cover) : ?>
                    <div class="bg" style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                    <?= Html::img($cover->getUrl(), ['height' => '530']) ?>
                <?php else : ?>
                    <div class="bg"
                            style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png' ?>')"></div>
                    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png', [
                        'height' => '530'
                    ]) ?>
                <?php endif ?>
                <!-- challenge image end -->

                <!-- campaign edit button start -->
                <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                    <?= Html::a('<i class="fa fa-pencil"></i>' . Yii::t('XcoinModule.challenge', 'Edit'), ['/xcoin/challenge/edit', 'id' => $challenge->id, 'container' => $this->context->contentContainer], ['data-target' => '#globalModal', 'class' => 'edit-btn']) ?>
                <?php endif; ?>
                <!-- campaign edit button end -->

                <?= SocialShare::widget(['url' => Yii::$app->request->hostInfo . Yii::$app->request->url]); ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="panel panel-default panel-body">
        <?php if ($challenge->with_location_filter === Challenge::CHALLENGE_LOCATION_FILTER_HIDDEN): ?>
            <div class="cs-categories">
                <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                    'challengeId' => $challenge->id
                ]); ?>" class="cs-category <?= !$activeCategory ? 'active' : '' ?>"><?= Yii::t('XcoinModule.challenge', 'All Projects') ?></a>
                <?php foreach ($categories as $category): ?>
                <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                    'challengeId' => $challenge->id,
                    'categoryId' => $category->id,
                ]); ?>" class="cs-category <?= $activeCategory == $category->id ? 'active' : '' ?>"><?= $category->name ?></a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="filters">
                <?php $form = ActiveForm::begin(['id' => 'filter-form']); ?>
                <div class="row">
                    <div class="col-md-3 categories">
                        <?= $form->field($model, 'category')->widget(Select2::class, [
                            'data' => ArrayHelper::map($categories, 'id', 'name'),
                            'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.challenge', 'Category') . ' - ', 'value' => $model->category ? $model->category : null],
                            'theme' => Select2::THEME_BOOTSTRAP,
                            'hideSearch' => true,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                            ],
                        ])->label(false) ?>
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
                                        <?= Yii::t('XcoinModule.challenge', 'Select location..') ?>
                                    <?php endif ?>
                                </span>
                                <span class="selection-arrow">
                                    <b></b>
                                </span>
                            </div>
                            <div class="location-dropdown">
                                <div class="dropdown-body">
                                    <div class="row">
                                        <div class="col-md-6"><?= Yii::t('XcoinModule.challenge', 'Country:') ?></div>
                                        <div class="col-md-6">
                                            <?=
                                            $form->field($model, 'country')->widget(Select2::class, [
                                                'data' => iso3166Codes::$countries,
                                                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.challenge', 'Select country') . ' - '],
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
                                        <div class="col-md-6"><?= Yii::t('XcoinModule.challenge', 'City:') ?></div>
                                        <div class="col-md-6">
                                            <?=
                                            $form->field($model, 'city')->textInput([
                                                'placeholder' => Yii::t('XcoinModule.challenge', 'Type your city name')
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
                    <div class="col-md-6 filter-actions">
                        <?= Html::submitButton(Yii::t('XcoinModule.challenge', 'Apply filter'), ['class' => 'sumbit btn btn-gradient-1']) ?>
                        <?= Html::Button(Yii::t('XcoinModule.challenge', 'Reset'), ['class' => 'reset btn btn-default']) ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        <?php endif; ?>
        <h3 class="header"><?= Yii::t('XcoinModule.challenge', 'Received Submissions') . ' (' . count($fundings) . ')' ?></h3>
        <div class="received-funding">
            <div class="row cs-cards">
                <?php if (count($fundings) == 0): ?>
                    <p class="alert alert-warning col-md-12">
                        <?= Yii::t('XcoinModule.challenge', 'Currently there are no received submissions.') ?>
                    </p>
                <?php endif; ?>
                <?php foreach ($fundings as $funding): ?>
                    <?php if ($funding->space->isAdmin(Yii::$app->user->identity) || $funding->published == Funding::FUNDING_PUBLISHED): ?>
                        <?php
                        $space = $funding->getSpace()->one();
                        $cover = $funding->getCover();
                        ?>
                        <div style="position: relative">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <a href="<?= $space->createUrl('/xcoin/funding/details', [
                                    'container' => $this->context->contentContainer,
                                    'fundingId' => $funding->id
                                ]); ?>" data-target="#globalModal">
                                    <div class="panel">
                                        <div class="panel-heading">
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
                                            </div>
                                        </div>
                                        <div class="panel-body <?= $funding->hidden_details && $funding->hidden_location ? 'sm' : ''?>">
                                            <h4 class="funding-title">
                                                <?= Html::encode($funding->title); ?>
                                                <?php if ($funding->review_status == Funding::FUNDING_NOT_REVIEWED) : ?>
                                                    <div style="color: orange; display: inline">
                                                        <i class="fa fa-check-circle-o" aria-hidden="true"
                                                            rel="tooltip"
                                                            title="<?= Yii::t('XcoinModule.funding', 'Under review') ?>"></i>
                                                    </div>
                                                <?php elseif ($funding->review_status == Funding::FUNDING_LAUNCHING_SOON) :?>
                                                    <div style="color: orange; display: inline">
                                                        <i class="fa fa-check-circle-o" aria-hidden="true"
                                                        rel="tooltip"
                                                        title="<?= Yii::t('XcoinModule.funding', 'Launching Soon') ?>"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <div style="color: dodgerblue; display: inline">
                                                        <i class="fa fa-check-circle-o" aria-hidden="true"
                                                            rel="tooltip"
                                                            title="<?= Yii::t('XcoinModule.funding', 'Verified') ?>"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </h4>
                                            <div class="media">
                                                <div class="media-left media-middle"></div>
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
                                                    <strong><?= $funding->getRaisedAmount() ?></strong>
                                                    (<strong><?= $funding->getRaisedPercentage() ?>%</strong>)
                                                    <!-- campaign raised end -->
                                                </div>
                                                <div class="pull-right">
                                                    <?php if($funding->review_status == Funding::FUNDING_LAUNCHING_SOON) :?>
                                                        <strong style="color: orange"><?= Yii::t('XcoinModule.funding', 'Launching soon') ?></strong>
                                                    <?php else : ?>
                                                    <!-- campaign remaining days start -->
                                                    <?php if ($funding->getRemainingDays() > 2) : ?>
                                                        <div class="clock"></div>
                                                    <?php else : ?>
                                                        <div class="clock red"></div>
                                                    <?php endif; ?>
                                                    <div class="days">
                                                        <?php if ($funding->getRemainingDays() > 0) : ?>
                                                            <strong><?= $funding->getRemainingDays() ?></strong> <?= $funding->getRemainingDays() > 1 ? Yii::t('XcoinModule.funding', 'Days left') : Yii::t('XcoinModule.funding', 'Day left') ?>
                                                        <?php else : ?>
                                                            <strong><?= Yii::t('XcoinModule.funding', 'Closed') ?></strong>
                                                        <?php endif; ?>
                                                    </div>
                                                    <?php endif; ?>

                                                    <!-- campaign remaining days end -->
                                                </div>
                                                <!-- campaign raised start -->
                                                <?php echo Progress::widget([
                                                    'percent' => $funding->getRaisedPercentage()
                                                ]); ?>
                                            </div>
                                            <div class="funding-details row">
                                                <div class="col-md-12">
                                                    <!-- campaign requesting start -->
                                                    <span>
                                                        <?= Yii::t('XcoinModule.funding', 'Requesting:') ?>
                                                        <strong><?= $funding->getRequestedAmount() ?></strong>
                                                    </span>
                                                    <?= SpaceImage::widget([
                                                        'space' => $challenge->asset->space,
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
                                </a>
                                <?php if (SpaceHelper::canReviewProject($funding->challenge->space) || PublicOffersHelper::canReviewSubmittedProjects()): ?>
                                    <?php if ($funding->review_status == Funding::FUNDING_NOT_REVIEWED) : ?>
                                        <?= Html::a('<i class="fa fa-close"></i>', ['/xcoin/challenge/review-funding', 'id' => $funding->id, 'status' => Funding::FUNDING_LAUNCHING_SOON, 'container' => $this->context->contentContainer], ['class' => 'review-btn-untrusted']) ?>
                                    <?php elseif ($funding->review_status == Funding::FUNDING_LAUNCHING_SOON) : ?>
                                        <?= Html::a('<i class="fa fa-check"></i>', ['/xcoin/challenge/review-funding', 'id' => $funding->id, 'status' => Funding::FUNDING_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-untrusted']) ?>
                                    <?php else : ?>
                                        <?= Html::a('<i class="fa fa-check"></i>', ['/xcoin/challenge/review-funding', 'id' => $funding->id, 'status' => Funding::FUNDING_NOT_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-trusted']) ?>
                                    <?php endif; ?>

                                    <?php if ($funding->hidden_location == Funding::FUNDING_LOCATION_HIDDEN) : ?>
                                        <?= Html::a('<i class="fa fa-map-marker"></i>', ['/xcoin/challenge/hide-funding-location', 'id' => $funding->id, 'status' => Funding::FUNDING_LOCATION_SHOWN, 'container' => $this->context->contentContainer], ['class' => 'location-btn-hidden']) ?>
                                    <?php else : ?>
                                        <?= Html::a('<i class="fa fa-map-marker"></i>', ['/xcoin/challenge/hide-funding-location', 'id' => $funding->id, 'status' => Funding::FUNDING_LOCATION_HIDDEN, 'container' => $this->context->contentContainer], ['class' => 'location-btn-shown']) ?>
                                    <?php endif; ?>

                                    <?php if ($funding->hidden_details == Funding::FUNDING_DETAILS_HIDDEN) : ?>
                                        <?= Html::a('<i class="fa fa-info-circle"></i>', ['/xcoin/challenge/hide-funding-details', 'id' => $funding->id, 'status' => Funding::FUNDING_DETAILS_SHOWN, 'container' => $this->context->contentContainer], ['class' => 'details-btn-hidden']) ?>
                                    <?php else : ?>
                                        <?= Html::a('<i class="fa fa-info-circle"></i>', ['/xcoin/challenge/hide-funding-details', 'id' => $funding->id, 'status' => Funding::FUNDING_DETAILS_HIDDEN, 'container' => $this->context->contentContainer], ['class' => 'details-btn-shown']) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
