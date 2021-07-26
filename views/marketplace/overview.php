<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\user\widgets\Image;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Marketplace;
use humhub\modules\xcoin\models\Product;
use yii\helpers\Html;
use humhub\modules\space\widgets\Image as SpaceImage;

Assets::register($this);

/** @var $marketplace Marketplace */
/** @var $products Product[] */

?>
<!-- TODO : move styling to css/less files, @craxrev -->

<style>
    .space-challenge .panel .panel-heading .categories {
        margin-top: 30px;
        text-transform: uppercase;
        color: #202020;
        display: inline-block;
    }

    .space-challenge .panel .panel-heading .categories + ul {
        list-style: none;
        display: inline-block;
        padding: 0;
    }

    .space-challenge .panel .panel-heading .categories + ul li {
        display: inline-block;
        padding: 6px 16px;
        box-shadow: 0 1px 3px #00000029;
        margin: 0 4px;
        border-radius: 16px;
    }
</style>

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
                        <?= Html::a(
                            $marketplace->isTasksMarketplace() ? Yii::t('XcoinModule.marketplace', 'Add your job') : Yii::t('XcoinModule.marketplace', 'Sell Your Product'), [
                            '/xcoin/product/new',
                            'marketplaceId' => $marketplace->id,
                            'container' => $this->context->contentContainer
                        ], ['class' => 'btn btn-gradient-1 add-project', 'data-target' => '#globalModal']); ?>
                    <?php endif; ?>
                </div>
                <?php if ($marketplace->getCategories()->count()) : ?>
                    <!-- product categories start -->
                    <?php if ($marketplace->isTasksMarketplace()) : ?>
                        <h6 class="categories"><?= Yii::t('XcoinModule.marketplace', 'Available jobs categories :') ?></h6>
                    <?php else : ?>
                        <h6 class="categories"><?= Yii::t('XcoinModule.marketplace', 'Available product categories :') ?></h6>
                    <?php endif; ?>
                    <ul>
                        <?php foreach ($marketplace->getCategories()->all() as $category) : ?>
                            <li>
                                <?= $category->name; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <!-- product categories end -->
                <?php endif; ?>
                <p class="challenge-description"><?= RichText::output($marketplace->description); ?></p>
            </div>
        </div>
        <div class="panel-body">
            <?php if ($marketplace->isTasksMarketplace()) : ?>
                <h3 class="header"><?= Yii::t('XcoinModule.marketplace', 'Suggested Jobs') ?></h3>
            <?php else : ?>
                <h3 class="header"><?= Yii::t('XcoinModule.marketplace', 'Suggested Products') ?></h3>
            <?php endif; ?>
            <div class="received-funding">
                <div class="row panels">
                    <?php if (count($products) == 0): ?>
                        <p class="alert alert-warning col-md-12">
                            <?= Yii::t('XcoinModule.marketplace', 'Currently there are no suggested products.') ?>
                        </p>
                    <?php endif; ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $owner = $marketplace->getSpace()->one();
                        $picture = $product->getPicture();
                        ?>
                        <div style="position: relative">
                            <div class="col-sm-6 col-md-4 col-lg-3">
                                <a href="<?= $owner->createUrl('/xcoin/product/details', [
                                    'productId' => $product->id
                                ]); ?>" data-target="#globalModal">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <!-- product picture start -->
                                            <?php if ($picture) : ?>
                                                <div class="bg"
                                                     style="background-image: url('<?= $picture->getUrl() ?>')"></div>
                                                <?= Html::img($picture->getUrl(), ['height' => '140']) ?>
                                            <?php else : ?>
                                                <div class="bg"
                                                     style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png' ?>')"></div>
                                                <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png', [
                                                    'height' => '140',
                                                    'width' => '320'
                                                ]) ?>
                                            <?php endif ?>
                                            <!-- product picture end -->
                                            <div class="project-owner" style="bottom: -33px">
                                                <!-- owner image start -->
                                                <?php if ($product->isSpaceProduct()): ?>
                                                    <?= SpaceImage::widget([
                                                        'space' => $product->getSpace()->one(),
                                                        'width' => 34,
                                                        'showTooltip' => false,
                                                        'link' => false
                                                    ]); ?>
                                                    <span><?= Yii::t('XcoinModule.product', 'By') . " <strong>" . Html::encode($product->getSpace()->one()->name) . "</strong>"; ?></span>
                                                <?php else : ?>
                                                    <?= Image::widget([
                                                        'user' => $product->getCreatedBy()->one(),
                                                        'width' => 34,
                                                        'showTooltip' => false,
                                                        'link' => false
                                                    ]); ?>
                                                    <span><?= Yii::t('XcoinModule.product', 'By') . " <strong>" . Html::encode($product->getCreatedBy()->one()->profile->firstname . " " . $product->getCreatedBy()->one()->profile->lastname) . "</strong>"; ?></span>
                                                <?php endif; ?>
                                                <!-- owner image end -->
                                            </div>
                                        </div>
                                        <div class="panel-body" style="margin-top: 38px;">
                                            <h4 class="funding-title" rel="tooltip"
                                                title="<?= str_replace('"', '&quot;', $product->name) ?>">
                                                <?= Html::encode($product->shortenName()); ?>
                                                <?php if ($product->review_status == Product::PRODUCT_NOT_REVIEWED) : ?>
                                                    <div style="color: orange; display: inline">
                                                        <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                                           title="<?= Yii::t('XcoinModule.marketplace', 'Under review') ?>"></i>
                                                    </div>
                                                <?php else: ?>
                                                    <div style="color: dodgerblue; display: inline">
                                                        <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                                           title="<?= Yii::t('XcoinModule.marketplace', 'Verified') ?>"></i>
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
                                                            <?= Yii::t('XcoinModule.marketplace', 'Price') ?> :
                                                            <b><?= $product->price ?></b>
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
                                </a>
                                <?php if (SpaceHelper::canReviewProject($product->marketplace->space) || PublicOffersHelper::canReviewSubmittedProjects()): ?>
                                    <?php if ($product->review_status == Product::PRODUCT_NOT_REVIEWED) : ?>
                                        <?= Html::a('<i class="fa fa-close"></i>', ['/xcoin/marketplace/review-product', 'id' => $product->id, 'status' => Product::PRODUCT_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-untrusted']) ?>
                                    <?php else : ?>
                                        <?= Html::a('<i class="fa fa-check"></i>', ['/xcoin/marketplace/review-product', 'id' => $product->id, 'status' => Product::PRODUCT_NOT_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-trusted']) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

