<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Funding;
use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Progress;
use yii\helpers\Url;

/** @var $fundings Funding[] */

Assets::register($this);
?>

<div class="container">
    <div class="dropdown">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"> <?= Yii::t('XcoinModule.base', 'Filter') ?>
            <span class="caret"></span></button>
        <ul class="dropdown-menu">
            <li><a href="<?= Url::to(['/xcoin/funding-overview', 'verified' => Funding::FUNDING_REVIEWED]) ?>"><?= Yii::t('XcoinModule.funding', 'Verified') ?></a></li>
            <li><a href="<?= Url::to(['/xcoin/funding-overview']) ?>"><?= Yii::t('XcoinModule.funding', 'Under review') ?></a></li>
        </ul>
    </div>
    <div class="row">
        <div class="pull-right sell-button">
            <?= Html::a(Yii::t('XcoinModule.funding', 'Add Your Project'), [
                '/xcoin/funding-overview/new',
            ], ['class' => 'btn btn-success btn-lg', 'data-target' => '#globalModal']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 fundingPanels">

            <?php if (count($fundings) == 0): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Yii::t('XcoinModule.funding', '<strong>Crowd</strong> Funding') ?>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-warning">
                            <?= Yii::t('XcoinModule.funding', 'Currently there are no running crowd fundings campaigns!') ?>
                        </div>
                    </div>
                    <br/>
                </div>
            <?php endif; ?>

            <div class="row">
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
                                                <h4 class="media-heading"><?= Html::encode($funding->shortenDescription()); ?></h4>
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
