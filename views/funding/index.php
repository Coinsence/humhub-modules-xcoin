<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Funding;
use yii\bootstrap\Html;
use yii\bootstrap\Progress;

/** @var $fundings Funding[] */

Assets::register($this);
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                <?= Html::a(Yii::t('XcoinModule.funding', 'Add asset offer'), [
                    '/xcoin/funding/edit',
                    'container' => $this->context->contentContainer
                ], ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>
        <?= Yii::t('XcoinModule.funding', '<strong>Crowd</strong> funding') ?>
    </div>

    <div class="panel-body">
        <?php if (count($fundings) == 0): ?>
            <br/>
            <p class="alert alert-warning">
                <?= Yii::t('XcoinModule.funding', 'Currently there are no open funding requests.') ?>
            </p>
        <?php else : ?>
            <p><?= Yii::t('XcoinModule.funding', 'The assets listed below are currently wanted as crowd funding investment.') ?></p>
        <?php endif; ?>

    </div>
</div>

<div class="row">
    <?php foreach ($fundings as $funding): ?>
        <?php
        $space = $funding->getSpace()->one();
        $cover = $funding->getCover();
        ?>

        <a href="<?= $space->createUrl('/xcoin/funding/overview', [
            'container' => $this->context->contentContainer,
            'fundingId' => $funding->id
        ]); ?>">
            <div class="col-md-4 crowd-funding">
                <div class="panel">
                    <div class="panel-heading">
                        <!-- campaign cover start -->
                        <?php if ($cover) : ?>
                            <div class="bg" style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                            <?= Html::img($cover->getUrl(), ['height' => '140']) ?>
                        <?php else : ?>
                            <div class="bg" style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>')"></div>
                            <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
                                'height' => '140',
                                'width' => '320'
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
                                <h4 class="media-heading"><?= Html::encode($funding->shortenDescription()); ?></h4>
                                <!-- campaign description end -->
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
                            <div class="col-md-6">
                                <!-- campaign requesting start -->
                                <span>
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
                            <div class="col-md-6">
                                <!-- campaign offering start -->
                                <span>
                                    <?= Yii::t('XcoinModule.funding', 'Offering:') ?>
                                    <strong><?= $funding->getOfferedAmountPercentage() ?>%</strong>
                                </span>
                                <?= SpaceImage::widget([
                                    'space' => $funding->space,
                                    'width' => 16,
                                    'showTooltip' => true,
                                    'link' => false
                                ]); ?>
                                <!-- campaign offering end -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    <?php endforeach; ?>
</div>
