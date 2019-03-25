<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\file\models\File;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\Funding;
use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Progress;

/**
 * @var $funding Funding
 */
?>

<div class="container">
    <div class="row">
        <div class="col-md-9 fundingPanels">
            <div class="row">
                <?php
                $space = Space::findOne(['id' => $funding->space_id]);
                $cover = File::find()->where(['object_model' => Funding::class, 'object_id' => $funding->id])->orderBy(['id' => SORT_ASC])->one();
                $gallery = File::find()->where(['object_model' => Funding::class, 'object_id' => $funding->id])->orderBy(['id' => SORT_DESC])->all();
                ?>

                <div class="col-md-12">
                    <div class="panel cover">
                        <div class="panel-heading">
                            <!-- campaign cover start -->
                            <div class="img-container">

                                <?php if ($cover) : ?>
                                    <?= Html::img($cover->getUrl(), ['width' => '100%']) ?>
                                <?php else : ?>
                                    <?= Html::img('https://www.bbsocal.com/wp-content/uploads/2017/07/Funding-icon.jpg', ['height' => '300', 'width' => '100%']) ?>
                                <?php endif ?>

                            </div>
                            <!-- campaign cover end -->
                            <!-- campaign invest action start -->
                            <div class="invest-btn">

                                <?php if (Yii::$app->user->isGuest): ?>
                                    <?= Html::a(Yii::t('XcoinModule.base', 'Invest in this project'), Yii::$app->user->loginUrl, ['data-target' => '#globalModal']) ?>
                                <?php else: ?>
                                    <?= Html::a(Yii::t('XcoinModule.base', 'Invest in this project'), ['invest', 'fundingId' => $funding->id, 'container' => $this->context->contentContainer], ['data-target' => '#globalModal', 'disabled' => $funding->canInvest()]); ?>
                                <?php endif; ?>

                            </div>
                            <!-- campaign invest action end -->
                        </div>
                        <div class="panel-body">

                            <!-- campaign title start -->
                            <h4 class="funding-title"><?=Html::encode($funding->title); ?></h4>
                            <!-- campaign title end -->

                            <div class="row">
                                <div class="col-md-7">
                                    <!-- campaign description start -->
                                    <p class="media-heading"><?= Html::encode($funding->description); ?></p>
                                    <!-- campaign description end -->
                                </div>
                                <div class="col-md-5 row funding-details">

                                    <div class="col-md-6">
                                        <!-- campaign requesting start -->
                                        Requesting :
                                        <strong><?= $funding->getRequestedAmount() ?></strong>
                                        <?= SpaceImage::widget(['space' => $funding->asset->space, 'width' => 24, 'showTooltip' => true, 'link' => true]); ?>
                                        <!-- campaign requesting end -->
                                    </div>
                                    <div class="col-md-6">
                                        <!-- campaign offering start -->
                                        Offering :
                                        <strong><?= $funding->getOfferedAmountPercentage() ?></strong>%
                                        <?= SpaceImage::widget(['space' => $funding->space, 'width' => 24, 'showTooltip' => true, 'link' => true]); ?>
                                        <!-- campaign offering end -->
                                    </div>






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

                                    <div class="clock"></div>

                                    <div class="days">
                                        <!-- campaign remaining days start -->
                                        <?= $funding->getRemainingDays() ?> <?= $funding->getRemainingDays() > 1 ? 'Days' : 'Day' ?> left
                                        <!-- campaign remaining days end -->
                                    </div>

                                </div>

                                <!-- campaign raised start -->
                                <?php echo Progress::widget([
                                        'percent' => $funding->getRaisedPercentage(),
                                ]); ?>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                  <div class="panel content">
                    <div class="panel-heading">
                        <!-- campaign gallery start -->
                        <div class="row">
                            <?php foreach ($gallery as $item): ?>
                                <div class="col-md-4">
                                    <?= Html::img($item->getUrl(), ['height' => '130']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <!-- campaign gallery end -->
                    </div>
                    <div class="panel-body">

                        <!-- campaign content start -->
                        <?= RichText::output($funding->content); ?>
                        <!-- campaign content end -->

                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

    .fundingPanels .panel.cover .panel-heading {
        padding: 0;
        text-align: center;
        position: relative;
        overflow: hidden;
        border-radius: 0;
    }

    .fundingPanels .panel.cover .panel-heading .img-container img{
        max-height: 400px;
    }

    .fundingPanels .panel.cover .panel-heading .invest-btn {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 0;
        -webkit-transition: height .25s ease-in-out;
        -moz-transition: height .25s ease-in-out;
        -ms-transition: height .25s ease-in-out;
        -o-transition: height .25s ease-in-out;
        transition: height .25s ease-in-out;
    }

    .fundingPanels .panel.cover .panel-heading .invest-btn a {
        -webkit-border-radius: 0;
        -moz-border-radius: 0;
        border-radius: 0;
        width: 100%;
        background: #28aa69;
        text-transform: uppercase;
        color: #fff;
        display: inline-block;
        padding: 14px 20px;
        font-size: 10px;
        font-weight: bold;
    }

    .fundingPanels .panel.cover .panel-heading:hover .invest-btn {
        height: 40px;
    }

    .fundingPanels .panel.cover .panel-body {
        padding: 24px;
    }

    .fundingPanels .panel.cover .panel-body .funding-title {
        font-size: 16px;
        font-weight: bold;
        color: #000;
    }

    .fundingPanels .panel.cover .panel-body .funding-details {
        border: #eaf1f6 solid 1px;
        box-shadow: 0 2px 18px 0 #ececec;
        -webkit-box-shadow: 0 2px 18px 0 #ececec;
        -moz-box-shadow: 0 2px 18px 0 #ececec;
    }

    .fundingPanels .panel.cover .panel-body .funding-details .col-md-6:first-of-type {
        border-right: #eaf1f6 solid 1px;
    }

    .fundingPanels .panel.cover .panel-body .funding-details .col-md-6 {
        padding: 8px 0;
        text-align: center;
        font-size: 14px;
    }

    .fundingPanels .panel.cover .panel-body .funding-details .col-md-6 a {
        margin-left: 8px;
    }

    .fundingPanels .panel.cover .panel-footer {
        background: none;
        border: none;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress {
        padding: 0 15px;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress > div:not(.progress) {
        display: inline-block;
        font-size: 12px;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress .clock::before {
        content: 'L';
        color: white;
        text-align: center;
        width: 100%;
        display: block;
        margin-left: 1px;
        font-size: 10px;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress .clock {
        display: inline-block;
        vertical-align: middle;
        width: 18px;
        height: 18px;
        border-radius: 18px;
        background: gray;
        margin-right: 4px;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress .days {
        display: inline-block;
        vertical-align: middle;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress .progress {
        width: 100%;
        height: 8px;
        margin-top: 12px;
        background: #e4e8eb;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress .progress-bar {
        background-color: #28aa69;
    }

    .fundingPanels .panel.content {

    }

    .fundingPanels .panel.content .panel-heading img {
        border-radius: 6px;
    }


</style>
