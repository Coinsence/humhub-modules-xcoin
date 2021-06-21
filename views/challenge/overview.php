<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\Funding;
use yii\helpers\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Progress;


Assets::register($this);

/** @var $challenge Challenge */
/** @var $fundings Funding[] */

?>

<div class="space-challenge">
    <div class="panel panel-default">
        <?php
        $space = $challenge->getSpace()->one();
        $cover = $challenge->getCover();
        ?>
        <div class="panel-heading">
            <div class="img-container">
                <!-- challenge image start -->
                <?php if ($cover) : ?>
                    <div class="bg" style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                    <?= Html::img($cover->getUrl(), ['height' => '550px']) ?>
                <?php else : ?>
                    <div class="bg"
                         style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png' ?>')"></div>
                    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-challenge-cover.png', [
                        'height' => '550'
                    ]) ?>
                <?php endif ?>

                <!-- challenge image end -->
                <?= Html::a('<span class="icon"><i class="X"></i></span>', ['/xcoin/challenge', 'container' => $this->context->contentContainer], ['class' => 'close-challenge']); ?>
                <!-- campaign edit button start -->
                <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                    <?= Html::a('<i class="fa fa-pencil"></i>' . Yii::t('XcoinModule.challenge', 'Edit'), ['/xcoin/challenge/edit', 'id' => $challenge->id, 'container' => $this->context->contentContainer], ['data-target' => '#globalModal', 'class' => 'edit-btn']) ?>
                <?php endif; ?>
                <!-- campaign edit button end -->
            </div>

            <div class="challenge-info">
                <h2 class="challenge-title"><?= $challenge->title ?></h2>
                <div class="middle-block">
                    <div class="challenge-asset">
                        <!-- challenge asset start -->
                        <?= SpaceImage::widget([
                            'space' => $challenge->asset->space,
                            'width' => 40,
                            'showTooltip' => true,
                            'link' => true
                        ]); ?>
                        <span class="asset-name"><?= $challenge->asset->space->name ?></span>
                        <!-- challenge asset end -->
                    </div>
                    <?php if ($challenge->isStopped()) : ?>
                        <div class="add-project" style="color: red">
                            <?= Yii::t('XcoinModule.challenge', 'This challenge is stopped') ?>
                        </div>
                    <?php else: ?>
                        <?php if (Yii::$app->user->isGuest): ?>
                            <?= Html::a(Yii::t('XcoinModule.challenge', 'Add Your Project'), Yii::$app->user->loginUrl, ['class' => 'btn btn-gradient-1 add-project', 'data-target' => '#globalModal']) ?>
                        <?php else: ?>
                            <?= Html::a(Yii::t('XcoinModule.challenge', 'Add Your Project'), [
                                '/xcoin/funding/new',
                                'challengeId' => $challenge->id,
                                'container' => $this->context->contentContainer
                            ], ['class' => 'btn btn-gradient-1 add-project', 'data-target' => '#globalModal']); ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <p class="challenge-description"><?= RichText::output($challenge->description); ?></p>
            </div>
        </div>
        <div class="panel-body">
            <h3 class="header"><?= Yii::t('XcoinModule.challenge', 'Received Submissions') ?></h3>
            <div class="received-funding">
                <div class="row panels">
                    <?php if (count($fundings) == 0): ?>
                        <p class="alert alert-warning col-md-12">
                            <?= Yii::t('XcoinModule.challenge', 'Currently there are no received submissions.') ?>
                        </p>
                    <?php endif; ?>
                    <?php foreach ($fundings as $funding): ?>
                        <?php if ($funding->space->isAdmin(Yii::$app->user->identity) || $funding->status !== Funding::FUNDING_STATUS_INVESTMENT_ACCEPTED): ?>
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
                                            <?= Html::a('<i class="fa fa-check"></i>', ['/xcoin/challenge/review-funding', 'id' => $funding->id, 'status' => Funding::FUNDING_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-trusted']) ?>
                                        <?php else : ?>
                                            <?= Html::a('<i class="fa fa-close"></i>', ['/xcoin/challenge/review-funding', 'id' => $funding->id, 'status' => Funding::FUNDING_NOT_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-untrusted']) ?>
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
</div>

