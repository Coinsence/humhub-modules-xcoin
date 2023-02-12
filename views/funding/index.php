<?php

use humhub\libs\Iso3166Codes;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Funding;
use yii\helpers\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Progress;


Assets::register($this);

/** @var $fundings Funding[] */


?>

<div class="space-fundings">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><?= Yii::t('XcoinModule.funding', 'Submitted proposals') ?></strong>
        </div>
        <div class="panel-body">
            <div class="panels">
                <?php if (count($fundings) == 0): ?>
                    <p class="alert alert-warning col-md-12">
                        <?= Yii::t('XcoinModule.funding', 'Currently there are no submitted proposals.') ?>
                    </p>
                <?php endif; ?>
                <?php foreach ($fundings as $funding): ?>
                    <?php
                    $space = $funding->getSpace()->one();
                    $cover = $funding->getCover();
                    ?>
                    <a href="<?= $space->createUrl('/xcoin/funding/overview', [
                            'fundingId' => $funding->id
                        ]); ?>">
                        <div class="col-sm-6 col-md-4 col-lg-3">
                            <div class="panel">
                                <div class="panel-heading">
                                    <!-- challenge image start -->
                                    <!-- <?//= ChallengeImage::widget(['challenge' => $funding->getChallenge()->one(), 'width' => 30, 'link' => true]) ?> -->
                                    <!-- challenge image end -->
                                    <!-- campaign cover start -->
                                    <?php if ($cover) : ?>
                                        <div class="bg" style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                                        <?= Html::img($cover->getUrl(), ['height' => '140']) ?>
                                    <?php else : ?>
                                        <div class="bg" style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>')"></div>
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
                                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.funding', 'Under review') ?>"></i>
                                            </div>
                                        <?php elseif ($funding->review_status == Funding::FUNDING_LAUNCHING_SOON) : ?>
                                            <div style="color: orange; display: inline">
                                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.funding', 'Launching soon') ?>"></i>
                                            </div>
                                        <?php else: ?>
                                            <div style="color: dodgerblue; display: inline">
                                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.funding', 'Verified') ?>"></i>
                                            </div>
                                        <?php endif; ?>
                                    </h4>
                                    <div class="media">
                                        <div class="media-left media-middle"></div>
                                        <div class="media-body">
                                            <!-- campaign description start -->
                                            <p class="media-heading"><?= Html::encode($funding->shortenDescription()); ?></p>
                                            <!-- campaign description end -->

                                            <!-- campaign location start -->
                                            <p class="funding-location"><i class="fa fa-map-marker"></i><?= Iso3166Codes::country($funding->country) . ', ' . $funding->city ?></p>
                                                <!-- campaign location end -->
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <div class="funding-progress">
                                        <div>
                                            <!-- campaign raised start -->
                                            <?= Yii::t('XcoinModule.funding', 'Raised:') ?> <strong><?= $funding->getRaisedAmount() ?></strong>
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
                                                'space' => $funding->getChallenge()->one()->asset->space,
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
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

