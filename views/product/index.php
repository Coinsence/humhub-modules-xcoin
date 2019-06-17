<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Product;
use yii\bootstrap\Html;

Assets::register($this);

/** @var $products Product[] */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                <?= Html::a(Yii::t('XcoinModule.product', 'Sell product'), [
                    '/xcoin/product/create',
                    'container' => $this->context->contentContainer
                ], ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>
        <strong><?= Yii::t('XcoinModule.product', 'Products') ?></strong>
    </div>

    <div class="panel-body">
        <p><?= Yii::t('XcoinModule.product', 'This is the list of space products.') ?></p>

        <?php if (count($products) === 0): ?>
            <br/>
            <p class="alert alert-warning">
                <?= Yii::t('XcoinModule.product', 'Currently there are no products.') ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <?php foreach ($products as $product): ?>
        <?php
        $space = $product->getSpace()->one();
        $picture = $product->getPicture();
        ?>

        <a href="<?= $space->createUrl('/xcoin/product/overview', [
            'container' => $this->context->contentContainer,
            'productId' => $product->id
        ]); ?>">
            <div class="col-md-4 crowd-funding">
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
                        <div class="funding-details large row">
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
                                        <?= Yii::t('XcoinModule.product', 'Price') ?> : <b><?= $product->price ?></b>
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
