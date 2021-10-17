
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

<div class="marketplacePortfolio panel panel-default">
    <div class="panel-heading">
        <span><?= Yii::t('XcoinModule.marketplace', '<strong>Marketplace</strong> Portfolio ') ?></span>
        <small><a href="<?= $user->createUrl('/xcoin/product') ?>">(<?= Yii::t('XcoinModule.marketplace', 'view all')?>)</a></small>
    </div>
    <div class="panel-body">
        <?php if (count($products) == 0): ?>
            <p class="alert alert-warning col-md-12">
                <?= Yii::t('XcoinModule.product', 'Currently you are not offering any product.') ?>
            </p>
        <?php endif; ?>
        <div class="space-fundings">
            <div class="panels">
                <?php foreach ($products as $product): ?>
                    <?php
                    $owner = $product->isSpaceProduct() ? $product->getSpace()->one() : $product->getCreatedBy()->one();
                    $picture = $product->getPicture();
                    ?>

                    <a href="<?= Url::to(['/xcoin/product/details','container' => $user,'productId' => $product->id ])?>"
                        data-target="#globalModal">

                        <div class="col-sm-6 col-md-4">
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
                                                <?php elseif ($product->offer_type == Product::OFFER_DISCOUNT_FOR_COINS) : ?>
                                                    <strong><?= $product->discount ?> %</strong>
                                                    <?= SpaceImage::widget([
                                                        'space' => $product->marketplace->asset->space,
                                                        'width' => 24,
                                                        'showTooltip' => true,
                                                        'link' => true
                                                    ]); ?>
                                                    <span><?= Yii::t('XcoinModule.product', 'Discount') ?></span>
                                                <?php else : ?>
                                                    <strong><?= $product->price ?></strong>
                                                    <?= SpaceImage::widget([
                                                        'space' => $product->marketplace->asset->space,
                                                        'width' => 24,
                                                        'showTooltip' => true,
                                                        'link' => true
                                                    ]); ?>
                                                    <span><?= Yii::t('XcoinModule.product', 'Per Voucher') ?></span>
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
