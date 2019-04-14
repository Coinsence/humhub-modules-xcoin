<?php


use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Product;
use yii\bootstrap\Html;

/** @var $products Product[] */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.base', 'Sell product'), [
                '/xcoin/marketplace/sell',
                'container' => $this->context->contentContainer
            ], ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']); ?>
        </div>
        <?= Yii::t('XcoinModule.base', '<strong>Your products</strong>'); ?>
    </div>

    <div class="panel-body">
        <p><?= Yii::t('XcoinModule.base', 'This is the list of your products.'); ?></p>

        <?php if (count($products) === 0): ?>
            <br/>
            <p class="alert alert-warning">
                <?= Yii::t('XcoinModule.base', 'Currently there are no products.'); ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <?php foreach ($products as $product): ?>
        <?php
        $user = Yii::$app->user->identity;
        $picture = $product->getPicture();
        ?>

        <a href="<?= $user->createUrl('/xcoin/product/overview', [
            'productId' => $product->id
        ]); ?>">
            <div class="col-md-4 crowd-funding">
                <div class="panel">
                    <div class="panel-heading">
                        <!-- product picture start -->
                        <?php if ($picture) : ?>
                            <?= Html::img($picture->getUrl(), ['height' => '140']) ?>
                        <?php else : ?>
                            <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
                                'height' => '140',
                                'width' => '320'
                            ]) ?>
                        <?php endif ?>
                        <!-- product picture end -->
                    </div>
                    <div class="panel-body">
                        <h4 class="funding-title"><?= Html::encode($product->name); ?></h4>
                        <div class="media">
                            <div class="media-left media-middle"></div>
                            <div class="media-body">
                                <!-- product description start -->
                                <h4 class="media-heading"><?= Html::encode($product->shortenDescription()); ?></h4>
                                <!-- product description end -->
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="funding-details row">
                            <div class="col-md-12">
                                <!-- product pricing & discount start -->
                                <div class="pull-left">
                                    <?= SpaceImage::widget([
                                        'space' => $product->asset->space,
                                        'width' => 30,
                                        'showTooltip' => true,
                                        'link' => false
                                    ]); ?>
                                </div>
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

    .layout-content-container .crowd-funding .panel-heading > img {
        width: 100%;
        border-top-right-radius: 4px;
        border-top-left-radius: 4px;
    }

    .layout-content-container .crowd-funding .panel-heading .project-owner {
        position: absolute;
        bottom: -18px;
        left: 0;
        right: 0;
        text-align: center;
    }

    .layout-content-container .crowd-funding .panel-heading .project-owner div.space-acronym {
        display: block;
        margin: 0 auto;
        border: white 2px solid;
    }

    .layout-content-container .crowd-funding .panel-heading .project-owner img.profile-user-photo {
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
        padding: 10px 15px;
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
