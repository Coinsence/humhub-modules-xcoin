<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Marketplace;
use humhub\modules\xcoin\models\Product;
use yii\helpers\Html;
use humhub\modules\space\widgets\Image as SpaceImage;

Assets::register($this);

/** @var $marketplace Marketplace */
/** @var $products Product[] */

?>

<div class="space-challenge">
    <div class="panel panel-default">
        <?php
        $space = $marketplace->getSpace()->one();
        $cover = $marketplace->getCover();
        ?>
        <div class="panel-heading">
            <div class="img-container">
                <!-- marketplace image start -->
                <?php if ($cover) : ?>
                    <div class="bg" style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                    <?= Html::img($cover->getUrl(), ['height' => '550px']) ?>
                <?php else : ?>
                    <div class="bg"
                         style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-marketplace-cover.png' ?>')"></div>
                    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-marketplace-cover.png', [
                        'height' => '550'
                    ]) ?>
                <?php endif ?>

                <!-- marketplace image end -->
                <?= Html::a('<span class="icon"><i class="X"></i></span>', ['/xcoin/marketplace', 'container' => $this->context->contentContainer], ['class' => 'close-challenge']); ?>
                <!-- marketplace edit button start -->
                <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                    <?= Html::a('<i class="fa fa-pencil"></i>' . Yii::t('XcoinModule.marketplace', 'Edit'), ['/xcoin/marketplace/edit', 'id' => $marketplace->id, 'container' => $this->context->contentContainer], ['data-target' => '#globalModal', 'class' => 'edit-btn']) ?>
                <?php endif; ?>
                <!-- marketplace edit button end -->
            </div>

            <div class="challenge-info">
                <h2 class="challenge-title"><?= $marketplace->title ?></h2>
                <div class="middle-block">
                    <div class="challenge-asset">
                        <!-- marketplace asset start -->
                        <?= SpaceImage::widget([
                            'space' => $marketplace->asset->space,
                            'width' => 40,
                            'showTooltip' => true,
                            'link' => true
                        ]); ?>
                        <span class="asset-name"><?= $marketplace->asset->space->name ?></span>
                        <!-- marketplace asset end -->
                    </div>
                    <?php if ($marketplace->isStopped()) : ?>
                        <div class="add-project" style="color: red">
                            <?= Yii::t('XcoinModule.marketplace', 'This marketplace is closed') ?>
                        </div>
                    <?php else: ?>
                        <?= Html::a(Yii::t('XcoinModule.marketplace', 'Sell Your Product'), [
                            '/xcoin/product/new',
                            'marketplaceId' => $marketplace->id,
                            'container' => $this->context->contentContainer
                        ], ['class' => 'btn btn-gradient-1 add-project', 'data-target' => '#globalModal']); ?>
                    <?php endif; ?>
                </div>
                <p class="challenge-description"><?= RichText::output($marketplace->description); ?></p>
            </div>
        </div>
        <div class="panel-body">
            <h3 class="header"><?= Yii::t('XcoinModule.marketplace', 'Suggested Products') ?></h3>
            <div class="received-funding">
                <div class="row panels">
                    <?php if (count($products) == 0): ?>
                        <p class="alert alert-warning col-md-12">
                            <?= Yii::t('XcoinModule.marketplace', 'Currently there are no suggested products.') ?>
                        </p>
                    <?php endif; ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                            $space = $product->getSpace()->one();
                            $picture = $product->getPicture();
                        ?>
                        <a href="<?= $space->createUrl('/xcoin/product/overview', [
                            'productId' => $product->id
                        ]); ?>">
                            <div class="col-sm-6 col-md-4 col-lg-3">
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
                                    </div>
                                    <div class="panel-body">
                                        <h4 class="funding-title">
                                            <?= Html::encode($product->name); ?>
                                            <?php if ($product->review_status == Product::PRODUCT_NOT_REVIEWED) : ?>
                                                <div style="color: orange; display: inline">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.marketplace', 'Under review') ?>"></i>
                                                </div>
                                            <?php else: ?>
                                                <div style="color: dodgerblue; display: inline">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.marketplace', 'Verified') ?>"></i>
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
                                                        <?= Yii::t('XcoinModule.marketplace', 'Price') ?> : <b><?= $product->price ?></b>
                                                        <?= SpaceImage::widget([
                                                            'space' => $product->marketplace->asset->space,
                                                            'width' => 24,
                                                            'showTooltip' => true,
                                                            'link' => false
                                                        ]); ?>
                                                        <small> <?= $product->getPaymentType() ?> </small>
                                                    <?php else : ?>
                                                        <?= $product->discount ?> %
                                                        <?= SpaceImage::widget([
                                                            'space' => $product->marketplace->asset->space,
                                                            'width' => 24,
                                                            'showTooltip' => true,
                                                            'link' => false
                                                        ]); ?>
                                                        <?= Yii::t('XcoinModule.marketplace', 'Discount') ?>
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
</div>

