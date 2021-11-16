<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\FundingCategory;
use yii\helpers\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\widgets\SocialShare;
use yii\bootstrap\Progress;

Assets::register($this);

/** @var $challenge Challenge */
/** @var $fundings Funding[] */
/** @var $categories FundingCategory[] */
/** @var $activeCategory number */

?>

<div class="cs-overview">
    <?= Html::a('<i class="fa fa-long-arrow-left"></i>&nbsp;&nbsp;', ['/xcoin/challenge', 'container' => $this->context->contentContainer], ['class' => 'close-cs-overview']); ?>
    <div class="panel panel-default panel-head">
        <?php
        $space = $challenge->getSpace()->one();
        $cover = $challenge->getCover();
        ?>

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
    <div class="panel panel-default panel-body">
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
            <?php foreach ($categories as $category): ?>
            <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $challenge->id,
                'categoryId' => $category->id,
            ]); ?>" class="cs-category <?= $activeCategory == $category->id ? 'active' : '' ?>"><?= $category->name ?></a>
            <?php endforeach; ?>
            <?php foreach ($categories as $category): ?>
            <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $challenge->id,
                'categoryId' => $category->id,
            ]); ?>" class="cs-category <?= $activeCategory == $category->id ? 'active' : '' ?>"><?= $category->name ?></a>
            <?php endforeach; ?>
            <?php foreach ($categories as $category): ?>
            <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $challenge->id,
                'categoryId' => $category->id,
            ]); ?>" class="cs-category <?= $activeCategory == $category->id ? 'active' : '' ?>"><?= $category->name ?></a>
            <?php endforeach; ?>
            <?php foreach ($categories as $category): ?>
            <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $challenge->id,
                'categoryId' => $category->id,
            ]); ?>" class="cs-category <?= $activeCategory == $category->id ? 'active' : '' ?>"><?= $category->name ?></a>
            <?php endforeach; ?>
            <?php foreach ($categories as $category): ?>
            <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $challenge->id,
                'categoryId' => $category->id,
            ]); ?>" class="cs-category <?= $activeCategory == $category->id ? 'active' : '' ?>"><?= $category->name ?></a>
            <?php endforeach; ?>
            <?php foreach ($categories as $category): ?>
            <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $challenge->id,
                'categoryId' => $category->id,
            ]); ?>" class="cs-category <?= $activeCategory == $category->id ? 'active' : '' ?>"><?= $category->name ?></a>
            <?php endforeach; ?>
            <?php foreach ($categories as $category): ?>
            <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $challenge->id,
                'categoryId' => $category->id,
            ]); ?>" class="cs-category <?= $activeCategory == $category->id ? 'active' : '' ?>"><?= $category->name ?></a>
            <?php endforeach; ?>
            <?php foreach ($categories as $category): ?>
            <a href="<?= $space->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $challenge->id,
                'categoryId' => $category->id,
            ]); ?>" class="cs-category <?= $activeCategory == $category->id ? 'active' : '' ?>"><?= $category->name ?></a>
            <?php endforeach; ?>
        </div>
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
                                        <div class="panel-body">
                                            <h4 class="funding-title">
                                                <?= Html::encode($funding->title); ?>
                                                <?php if ($funding->review_status == Funding::FUNDING_NOT_REVIEWED) : ?>
                                                    <div style="color: orange; display: inline">
                                                        <i class="fa fa-check-circle-o" aria-hidden="true"
                                                            rel="tooltip"
                                                            title="<?= Yii::t('XcoinModule.funding', 'Under review') ?>"></i>
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
                                                </div>
                                            </div>
                                        </div>
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
                                    </div>
                                </a>
                                <?php if (SpaceHelper::canReviewProject($funding->challenge->space) || PublicOffersHelper::canReviewSubmittedProjects()): ?>
                                    <?php if ($funding->review_status == Funding::FUNDING_NOT_REVIEWED) : ?>
                                        <?= Html::a('<i class="fa fa-close"></i>', ['/xcoin/challenge/review-funding', 'id' => $funding->id, 'status' => Funding::FUNDING_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-untrusted']) ?>
                                    <?php else : ?>
                                        <?= Html::a('<i class="fa fa-check"></i>', ['/xcoin/challenge/review-funding', 'id' => $funding->id, 'status' => Funding::FUNDING_NOT_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-trusted']) ?>
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
