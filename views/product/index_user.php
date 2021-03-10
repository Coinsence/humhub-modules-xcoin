<?php

use humhub\modules\user\widgets\Image;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Product;
use yii\bootstrap\Html;
use yii\helpers\Url;

Assets::register($this);

/** @var $products Product[] */
?>
<!-- TODO move styles to less files -->
<style>
    .add-challenge {
        height: 300px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        border: 1px #cecece dashed;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .add-challenge .icon {
        display: block;
        background-color: #e2f7ff;
        padding: 20px 30px;
        border-radius: 50%;
    }
    .add-challenge .icon .cross {
        position: relative;
        display: block;
        background-color: #3cbeef;
        height: 23px;
        width: 3px;
    }
    .add-challenge .icon .cross:after {
        position: absolute;
        content: '';
        background-color: #3cbeef;
        height: 3px;
        width: 23px;
        left: -10px;
        top: 10px;
    }
    .add-challenge .text {
        margin-top: 24px;
        font-size: 18px;
        color: #202020;
    }
</style>
<div class="space-fundings">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><?= Yii::t('XcoinModule.product', 'Offered products') ?></strong>
        </div>
        <div class="panel-body">
            <div class="panels">
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <a class="add-challenge " href="<?= Url::to(['/xcoin/marketplace-overview/new']) ?>"
                       data-target="#globalModal">
                        <span class="icon">
                            <i class="cross"></i>
                        </span>
                        <span class="text"><?= Yii::t('XcoinModule.marketplace', 'Sell Your Product!') ?></span>
                    </a>
                </div>
                <?php if (count($products) == 0): ?>
                    <p class="alert alert-warning col-md-12">
                        <?= Yii::t('XcoinModule.product', 'Currently you are not offering any product.') ?>
                    </p>
                <?php endif; ?>
                <?php foreach ($products as $product): ?>
                    <?php
                    $owner = $product->getCreatedBy()->one();
                    $picture = $product->getPicture();
                    ?>

                    <a href="<?= $owner->createUrl('/xcoin/product/overview', [
                        'container' => $this->context->contentContainer,
                        'productId' => $product->id
                    ]); ?>">
                        <div class="col-md-3 crowd-funding">
                            <div class="panel">
                                <div class="panel-heading">
                                    <!-- product picture start -->
                                    <?php if ($picture) : ?>
                                        <div class="bg" style="background-image: url('<?= $picture->getUrl() ?>')"></div>
                                        <?= Html::img($picture->getUrl(), ['height' => '140']) ?>
                                    <?php else : ?>
                                        <div class="bg" style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png' ?>')"></div>
                                        <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png', [
                                            'height' => '140',
                                            'width' => '320'
                                        ]) ?>
                                    <?php endif ?>
                                    <!-- product picture end -->
                                    <div class="project-owner">
                                        <!-- user image start -->
                                        <?= Image::widget([
                                            'user' => $product->getCreatedBy()->one(),
                                            'width' => 34,
                                            'showTooltip' => false,
                                            'link' => false
                                        ]); ?>
                                        <!-- user image end -->
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <h4 class="funding-title" rel="tooltip" title="<?= str_replace('"', '&quot;',$product->name) ?>">
                                        <?= Html::encode($product->shortenName()); ?>
                                        <?php if ($product->review_status == Product::PRODUCT_NOT_REVIEWED) : ?>
                                            <div style="color: orange; display: inline">
                                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.product', 'Under review') ?>"></i>
                                            </div>
                                        <?php else: ?>
                                            <div style="color: dodgerblue; display: inline">
                                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.product', 'Verified') ?>"></i>
                                            </div>
                                        <?php endif; ?>
                                    </h4>
                                    <div class="media">
                                        <div class="media-left media-middle"></div>
                                        <div class="media-body">
                                            <!-- product description start -->
                                            <p class="media-heading"><?= Html::encode($product->shortenDescription()); ?></p>
                                            <!-- product description end -->
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <div class="funding-details large row">
                                        <div class="col-md-12">
                                            <!-- product pricing & discount start -->
                                            <div class="text-center">
                                                <?php if ($product->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS) : ?>
                                                    <?= Yii::t('XcoinModule.product', 'Price') ?> : <b><?= $product->price ?></b>
                                                    <?= SpaceImage::widget([
                                                        'space' => $product->marketplace->asset->space,
                                                        'width' => 24,
                                                        'showTooltip' => true,
                                                        'link' => false
                                                    ]); ?>
                                                    <small> <?= $product->getPaymentType() ?> </small>
                                                <?php else : ?>
                                                    <?= $product->discount ?> % <?= Yii::t('XcoinModule.product', 'Discount') ?>
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
        </div>
    </div>
</div>