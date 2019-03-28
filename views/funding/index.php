<?php

use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Html;
use yii\bootstrap\Progress;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                <?= Html::a(Yii::t('XcoinModule.base', 'Add asset offer'), [
                    '/xcoin/funding/edit',
                    'container' => $this->context->contentContainer
                ], ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>
        <?= Yii::t('XcoinModule.base', '<strong>Crowd</strong> funding'); ?>
    </div>

    <div class="panel-body">
        <p><?= Yii::t('XcoinModule.base', 'The assets listed below are currently wanted as crowd funding investment.'); ?></p>

        <?php if (count($activeFundings) === 0): ?>
            <br/>
            <p class="alert alert-warning">
                <?= Yii::t('XcoinModule.base', 'Currently there are no open funding requests.'); ?>
            </p>
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
                            <?= Html::img($cover->getUrl(), ['height' => '140']) ?>
                        <?php else : ?>
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
                        <h4 class="funding-title"><?= Html::encode($funding->title); ?></h4>
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
                                Raised: <strong><?= $funding->getRaisedAmount() ?></strong>
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
                                        <strong><?= $funding->getRemainingDays() ?></strong> <?= $funding->getRemainingDays() > 1 ? 'Days' : 'Day' ?>
                                        left
                                    <?php else : ?>
                                        <strong>Closed</strong>
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
                                    Requesting:
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
                                    Offering:
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

<style>

    .layout-content-container .crowd-funding .panel {
        border-radius: 8px;
        position: relative;
        transition: transform 0.3s ease-in-out;
    }

    .layout-content-container .crowd-funding .panel::after {

        content: '';
        position: absolute;

        width: 100%;
        height: 100%;
        top: 0;
        left: 0;

        box-shadow: 0 0 40px #c5c5c5;
        -webkit-box-shadow: 0 0 40px #c5c5c5;
        -moz-box-shadow: 0 0 40px #c5c5c5;

        opacity: 0;
        transition: opacity 0.3s ease-in-out;
    }


    .layout-content-container .crowd-funding .panel:hover {
        transform: translate(0, -5px);
    }

    .layout-content-container .crowd-funding .panel:hover::after {
        opacity: 1;
    }

    .layout-content-container .crowd-funding .panel:hover::after {
        opacity: 1;
    }

    .layout-content-container .crowd-funding .panel-heading {
        padding: 0;
        position: relative;
    }

    .layout-content-container .crowd-funding .panel-heading img {
        width: 100%;
        border-top-right-radius: 4px;
        border-top-left-radius: 4px;
    }

    .layout-content-container .crowd-funding .panel-heading .project-owner {
        position: absolute;
        bottom: -18px;
        left: 0;
        right: 0;
    }

    .layout-content-container .crowd-funding .panel-heading .project-owner div.space-acronym {
        display: block;
        margin: 0 auto;
        border: white 2px solid;
    }

    .layout-content-container .crowd-funding .panel-heading .project-owner span {
        display: block;
        width: 100%;
        text-align: center;
        font-size: 12px;
    }

    .layout-content-container .crowd-funding .panel-heading .project-owner strong {
        font-weight: 600;
    }

    .layout-content-container .crowd-funding .panel-body {
        margin-top: 20px;
        height: 100px;
    }

    .layout-content-container .crowd-funding .panel-body .funding-title {
        text-align: center;
        font-weight: bold;
        font-size: 14px;
        margin: 0;
    }

    .layout-content-container .crowd-funding .panel-body .media {
        margin-top: 6px;
    }

    .layout-content-container .crowd-funding .panel-body .media h4.media-heading {
        font-size: 12px;
        line-height: 16px;
        text-align: center;
    }

    .layout-content-container .crowd-funding .panel-footer {
        background-color: white;
        border: none;
        padding: 0;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-progress {
        padding: 0 15px;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-progress > div:not(.progress) {
        display: inline-block;
        font-size: 10px;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-progress .clock::before {
        content: 'L';
        color: white;
        text-align: center;
        width: 100%;
        display: block;
        margin-left: 1px;
        font-size: 10px;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-progress .clock {
        display: inline-block;
        vertical-align: middle;
        width: 18px;
        height: 18px;
        border-radius: 18px;
        background: gray;
        margin-right: 4px;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-progress .clock.red {
        background: red;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-progress .days {
        display: inline-block;
        vertical-align: middle;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-progress .clock.red + .days {
        color: red;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-progress .progress {
        width: 100%;
        height: 6px;
        margin-top: 3px;
        background: #e4e8eb;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-progress .progress-bar {
        background-color: #28aa69;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-details {
        padding: 0 15px;
        border-top: 1px solid #f0f5f8;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-details .col-md-6 {
        padding: 12px 2px 12px 15px;
        font-size: 12px;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-details .col-md-6:first-of-type {
        border-right: 1px solid #f0f5f8;
    }

    .layout-content-container .crowd-funding .panel-footer .funding-details .col-md-6 span {
        vertical-align: middle;
        margin-right: 2px;
    }

</style>
