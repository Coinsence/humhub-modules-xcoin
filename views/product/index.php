<?php

use humhub\modules\user\widgets\Image;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Product;
use yii\bootstrap\Html;

Assets::register($this);

/** @var $products Product[] */

?>

<div class="space-fundings">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong><?= Yii::t('XcoinModule.product', 'Offered products') ?></strong>
        </div>
        <div class="panel-body">
            <div class="panels">
                <?php if (count($products) == 0): ?>
                    <p class="alert alert-warning col-md-12">
                        <?= Yii::t('XcoinModule.product', 'Currently there are no offered products.') ?>
                    </p>
                <?php endif; ?>
                <?php foreach ($products as $product): ?>
                    <?php
                    $space = $product->getSpace()->one();
                    $picture = $product->getPicture();
                    ?>

                    <a href="<?= $space->createUrl('/xcoin/product/overview', [
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
                                        <!-- owner image start -->
                                        <?php if($product->isSpaceProduct()): ?>
                                            <?= SpaceImage::widget([
                                                'space' => $product->getSpace()->one(),
                                                'width' => 34,
                                                'showTooltip' => true,
                                                'link' => false
                                            ]); ?>
                                        <?php else : ?>
                                        <?= Image::widget([
                                            'user' => $product->getCreatedBy()->one(),
                                            'width' => 34,
                                            'showTooltip' => true,
                                            'link' => false
                                        ]); ?>
                                        <?php endif; ?>
                                        <!-- owner image end -->
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
                                                    <?= $product->discount ?> % <?= Yii::t('XcoinModule.product', 'Discount') ?>
                                                <?php else: ?>
                                                    <?= Yii::t('XcoinModule.product', 'Price') ?> : <b><?= $product->price ?></b>
                                                    <?= SpaceImage::widget([
                                                        'space' => $product->marketplace->asset->space,
                                                        'width' => 24,
                                                        'showTooltip' => true,
                                                        'link' => false
                                                    ]); ?>
                                                    <small> <?= Yii::t('XcoinModule.product', 'Per Voucher') ?> </small>
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
