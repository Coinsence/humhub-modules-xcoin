<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\widgets\BuyProductButton;
use humhub\widgets\TimeAgo;
use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
/**
 * @var $product Product
 */
?>

<div class="container">
    <div class="row">
        <div class="col-md-9 fundingPanels">
            <div class="row">
                <?php
                $space = $product->getSpace()->one();
                $picture = $product->getPicture();
                ?>

                <div class="col-md-12">
                    <div class="panel cover">
                        <div class="panel-heading">
                            <!-- product image start -->
                            <div class="img-container">

                                <?php if ($picture) : ?>
                                    <?= Html::img($picture->getUrl(), ['width' => '100%']) ?>
                                <?php else : ?>
                                    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
                                        'width' => '100%'
                                    ]) ?>
                                <?php endif ?>

                            </div>
                            <!-- product image end -->
                            <!-- product buy action start -->
                            <div class="invest-btn">
                                <?= BuyProductButton::widget(['guid' => $product->getCreatedBy()->one()->guid])?>
                            </div>
                            <!-- product buy action end -->
                            <!-- product edit button start -->

                            <?php if (AssetHelper::canManageAssets($this->context->contentContainer) || $product->isOwner(Yii::$app->user->identity)): ?>
                                <?= Html::a(Yii::t('XcoinModule.base', '<i class="fa fa-pencil"></i>Edit'), ['/xcoin/product/edit', 'id' => $product->id, 'container' => $this->context->contentContainer], ['data-target' => '#globalModal', 'class' => 'edit-btn']) ?>
                            <?php endif; ?>
                            <!-- product edit button end -->

                        </div>
                        <div class="panel-body">

                            <!-- product name start -->
                            <h4 class="funding-title"><?= Html::encode($product->name); ?></h4>
                            <!-- product name end -->

                            <div class="row">
                                <div class="col-md-6">
                                    <!-- product description start -->
                                    <p class="media-heading"><?= Html::encode($product->description); ?></p>
                                    <!-- product description end -->
                                </div>
                                <div class="col-md-3"></div>
                                <div class="col-md-3">
                                    <div class="col-md-12 funding-details">
                                        <!-- product pricing & discount start -->
                                        <?= SpaceImage::widget([
                                            'space' => $product->asset->space,
                                            'width' => 30,
                                            'showTooltip' => true,
                                            'link' => false
                                        ]); ?>
                                        <div class="text-center">
                                            <?php if ($product->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS) : ?>
                                                Price : <?= $product->price ?>
                                            <?php else : ?>
                                                <?= $product->discount ?> % Discount
                                            <?php endif; ?>
                                        </div>
                                        <!-- product pricing & discount end -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">
                            <?= $product->status ? 'Available' : 'Unavailable' ?> |
                            <?= Html::icon('time') ?>
                            <?= TimeAgo::widget(['timestamp' => $product->created_at]); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="panel content">
                        <div class="panel-body">
                            <!-- product content start -->
                            <?= RichText::output($product->content); ?>
                            <!-- product content end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>

    .fundingPanels .panel.cover {
        overflow: hidden;
    }

    .fundingPanels .panel.cover .panel-heading {
        padding: 0;
        text-align: center;
        position: relative;
        overflow: hidden;
        border-radius: 0;
    }

    .fundingPanels .panel.cover .panel-heading .img-container img {
        max-height: 400px;
    }

    .fundingPanels .panel.cover .panel-heading .invest-btn {
        width: 100%;
    }

    .fundingPanels .panel.cover .panel-heading .invest-btn button {
        -webkit-border-radius: 0;
        -moz-border-radius: 0;
        border-radius: 0;
        width: 100%;
        background: #28aa69;
        text-transform: uppercase;
        color: #fff;
        border: 0;
        display: inline-block;
        padding: 26px 20px;
        font-size: 17px;
        font-weight: bold;
    }

    .fundingPanels .panel.cover .panel-heading .invest-btn button:hover {
        background: #25a264;
    }

    .fundingPanels .panel.cover .panel-heading .invest-btn.disabled {
        cursor: not-allowed;
    }

    .fundingPanels .panel.cover .panel-heading .invest-btn.disabled button {
        pointer-events: none;
        display: inline-block;
        opacity: 0.5;
    }

    .fundingPanels .panel.cover .panel-heading .edit-btn {
        position: absolute;
        top: 18px;
        left: 18px;
        color: black;
        background: white;
        border-radius: 20px;
        font-size: 14px;
        padding: 4px 12px;
        font-weight: bold;
    }

    .fundingPanels .panel.cover .panel-heading .edit-btn:hover {
        background: #cecece;
    }

    .fundingPanels .panel.cover .panel-heading .edit-btn i {
        margin-right: 6px;
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
        box-shadow: 0 1px 0px 0 #c5c5c5;
        -webkit-box-shadow: 0 2px 2px 0 #c5c5c5;
        -moz-box-shadow: 0 1px 0px 0 #c5c5c5;
        padding: 10px 18px;
        border-radius: 8px;
        margin-bottom: 12px;
        text-align: center;
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

    .fundingPanels .panel.cover .panel-footer .funding-progress .clock.red {
        background: red;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress .days {
        display: inline-block;
        vertical-align: middle;
        color: gray;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress .clock.red + .days {
        color: red;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress .progress {
        width: 100%;
        height: 8px;
        margin-top: 3px;
        background: #e4e8eb;
    }

    .fundingPanels .panel.cover .panel-footer .funding-progress .progress-bar {
        background-color: #28aa69;
    }

    .fundingPanels .panel.content .panel-heading {
        padding: 20px 10px;
    }

    .fundingPanels .panel.content .panel-heading .col-md-4 {
        text-align: center;
    }

    .fundingPanels .panel.content .panel-heading img {
        border-radius: 6px;
    }


</style>
