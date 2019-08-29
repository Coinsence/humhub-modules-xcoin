<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\Product;
use yii\bootstrap\Html;

Assets::register($this);

/** @var $products Product[] */

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.product', 'Sell product'), [
                '/xcoin/marketplace/sell',
                'container' => $this->context->contentContainer
            ], ['class' => 'btn btn-gradient-1 btn-sm', 'data-target' => '#globalModal']); ?>
        </div>
        <strong><?= Yii::t('XcoinModule.product', 'Your products') ?></strong>
    </div>

    <div class="panel-body">
        <p><?= Yii::t('XcoinModule.product', 'This is the list of your products.') ?></p>

        <?php if (count($products) === 0): ?>
            <br/>
            <p class="alert alert-warning">
                <?= Yii::t('XcoinModule.product', 'Currently there are no products.') ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="content panel panel-default">
            <?php foreach ($products as $product): ?>
                <?php
                $user = Yii::$app->user->identity;
                $picture = $product->getPicture();
                ?>

                <a href="<?= $user->createUrl('/xcoin/product/overview', [
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
                                    <div class="bg" style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>')"></div>
                                    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
                                        'height' => '140',
                                        'width' => '320'
                                    ]) ?>
                                <?php endif ?>
                                <!-- product picture end -->
                            </div>
                            <div class="panel-body">
                                <h4 class="funding-title">
                                    <?= Html::encode($product->name); ?>
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
                                                    'space' => $product->asset->space,
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
