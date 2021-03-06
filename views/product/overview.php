<?php

use humhub\modules\user\widgets\Image;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\widgets\BuyProductButton;
use humhub\modules\xcoin\widgets\MarketplaceImage;
use yii\bootstrap\Carousel;
use humhub\libs\Html;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\PublicOffersHelper;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\libs\Iso3166Codes;
use humhub\modules\content\widgets\richtext\RichText;


Assets::register($this);

/**
 * @var $product Product
 */
?>

<div class="space-funding">
    <?php
    $cover = $product->getPicture();
    $gallery = $product->getGallery();

    $carouselItems = [];

    $coverItemUrl = '';

    if ($cover):
        $coverItemUrl = $cover->getUrl();
    else:
        $coverItemUrl = Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png';
    endif;

    $coverItem = "<div class=\"carousel-item\">";
    $coverItem .= "<div class=\"bg\" style=\"background-image: url('{$coverItemUrl}')\"></div>";
    $coverItem .= Html::img($coverItemUrl, ['width' => '100%']);
    $coverItem .= "</div>";

    $carouselItems[] = $coverItem;

    foreach ($gallery as $item):

        $carouselItem = "<div class=\"carousel-item\">";
        $carouselItem .= "<div class=\"bg\" style=\"background-image: url('{$item->getUrl()}')\"></div>";
        $carouselItem .= Html::img($item->getUrl(), ['width' => '100%']);
        $carouselItem .= "</div>";

        $carouselItems[] = $carouselItem;
    endforeach;
    ?>
    <div class="panel">
        <div class="panel-heading">
            <!-- product cover start -->
            <div class="img-container">

                <?php if ($cover) : ?>
                    <?php if (count($carouselItems) > 1): ?>
                        <?= Carousel::widget([
                            'items' => $carouselItems,
                        ]) ?>
                    <?php else: ?>
                        <div class="bg"
                             style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                        <?= Html::img($cover->getUrl(), [
                            'width' => '100%'
                        ]) ?>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="bg"
                         style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png' ?>')"></div>
                    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png', [
                        'width' => '100%'
                    ]) ?>
                <?php endif ?>
            </div>
            <!-- product cover end -->

            <!-- marketplace image start -->
            <?= MarketplaceImage::widget(['marketplace' => $product->marketplace, 'width' => 30, 'link' => true, 'linkOptions' => ['class' => 'challenge-btn']]) ?>
            <!-- marketplace image end -->

            <!-- product buttons start -->
            <?php if (AssetHelper::canManageAssets($this->context->contentContainer) || $product->isOwner(Yii::$app->user->identity)): ?>
                <?= Html::a(
                    '<i class="fa fa-pencil"></i>' . Yii::t('XcoinModule.product', 'Edit'),
                    [
                        '/xcoin/product/edit',
                        'id' => $product->id,
                        'container' => $this->context->contentContainer
                    ],
                    [
                        'data-target' => '#globalModal',
                        'class' => 'edit-btn',
                        'title' => 'Edit product details'
                    ]
                ) ?>
            <?php endif; ?>
            <!-- product buttons end -->

            <!-- product review button start -->
            <?php if (SpaceHelper::canReviewProject($product->marketplace->space) || PublicOffersHelper::canReviewSubmittedProjects()): ?>
                <?php if ($product->review_status == Product::PRODUCT_NOT_REVIEWED) : ?>
                    <?= Html::a('<i class="fa fa-check"></i> ' . Yii::t('XcoinModule.product', 'Trusted'), ['/xcoin/product/review', 'id' => $product->id, 'status' => Product::PRODUCT_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-trusted pull-right']) ?>
                <?php else : ?>
                    <?= Html::a('<i class="fa fa-close"></i> ' . Yii::t('XcoinModule.product', 'Untrusted'), ['/xcoin/product/review', 'id' => $product->id, 'status' => Product::PRODUCT_NOT_REVIEWED, 'container' => $this->context->contentContainer], ['class' => 'review-btn-untrusted pull-right']) ?>
                <?php endif; ?>
            <?php endif; ?>
            <!-- product review button end -->
        </div>
        <div class="panel-body">
            <!-- product title start -->
            <h4 class="funding-title">
                <?= Html::encode($product->name); ?>
                <!-- product review status start -->
                <small>
                    <?php if ($product->review_status == Product::PRODUCT_NOT_REVIEWED) : ?>
                        <div style="color: orange; display: inline">
                            ( <i class="fa fa-check-circle-o"
                                 aria-hidden="true"></i> <?= Yii::t('XcoinModule.product', 'Under review') ?>
                            )
                        </div>
                    <?php else: ?>
                        <div style="color: dodgerblue; display: inline">
                            ( <i class="fa fa-check-circle-o"
                                 aria-hidden="true"></i> <?= Yii::t('XcoinModule.product', 'Verified') ?>
                            )
                        </div>
                    <?php endif; ?>
                </small>
                <!-- product review status end -->
            </h4>
            <!-- product title end -->


            <div class="value">
                <?= Yii::t('XcoinModule.product', 'Owner:') ?>
                <span>
                    <!-- owner image start -->
                    <?php if ($product->isSpaceProduct()): ?>
                        <?= SpaceImage::widget([
                            'space' => $product->getSpace()->one(),
                            'width' => 34,
                            'showTooltip' => false,
                            'link' => false
                        ]); ?>
                        <?= " <strong>" . Html::encode($product->getSpace()->one()->name) . "</strong>"; ?>
                    <?php else : ?>
                        <?= Image::widget([
                            'user' => $product->getCreatedBy()->one(),
                            'width' => 34,
                            'showTooltip' => false,
                            'link' => false
                        ]); ?>
                        <?= " <strong>" . Html::encode($product->getCreatedBy()->one()->profile->firstname . " " . $product->getCreatedBy()->one()->profile->lastname) . "</strong>"; ?>
                    <?php endif; ?>
                    <!-- owner image end -->
                </span>
            </div>

            <!-- product pricing start -->
            <h6 class="value">
                <?= Yii::t('XcoinModule.marketplace', 'Price') ?> :
                <?php if ($product->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS) : ?>
                    <b><?= $product->price ?></b>
                    <?= SpaceImage::widget([
                        'space' => $product->marketplace->asset->space,
                        'width' => 24,
                        'showTooltip' => true,
                        'link' => true
                    ]); ?>
                    <small> <?= $product->getPaymentType() ?> </small>
                <?php else : ?>
                    <b><?= $product->discount ?> %</b>
                    <?= SpaceImage::widget([
                        'space' => $product->marketplace->asset->space,
                        'width' => 24,
                        'showTooltip' => true,
                        'link' => true
                    ]); ?>
                    <?= Yii::t('XcoinModule.marketplace', 'Discount') ?>
                <?php endif; ?>
            </h6>
            <!-- product pricing end -->

            <!-- product location start -->
            <h6 class="location">
                <?= Yii::t('XcoinModule.product', 'Location:') ?>
                <strong><?= Iso3166Codes::country($product->country) . ', ' . $product->city ?></strong>
            </h6>
            <!-- product location end -->


            <!-- product categories start -->
            <h6 class="categories"><?= Yii::t('XcoinModule.product', 'Categories:') ?></h6>
            <ul>
                <?php foreach ($product->getCategories()->all() as $category) : ?>
                    <li>
                        <?= $category->name; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <!-- product categories end -->

            <!-- product description start -->
            <div class="description row">
                <div class="col-md-12">
                    <p class="media-heading"><?= Html::encode($product->description); ?></p>
                </div>
            </div>
            <!-- product description end -->

            <!-- product content start -->
            <?= RichText::output($product->content); ?>
            <!-- product content end -->
        </div>
        <div class="panel-footer">
            <!-- product buy action start -->
            <?php if ($product->status == Product::STATUS_UNAVAILABLE || $product->isOwner(Yii::$app->user->identity)): ?>
            <div class="invest-btn disabled">
                <?php else: ?>
                <div class="invest-btn">
                    <?php endif; ?>
                    <?php if (Yii::$app->user->isGuest): ?>
                        <?= Html::a(Yii::t('XcoinModule.product', 'Buy this product'), Yii::$app->user->loginUrl, ['data-target' => '#globalModal']) ?>
                    <?php else: ?>
                        <?php if ($product->isPaymentFirst()) : ?>
                            <?= Html::a(
                                $product->marketplace->action_name ? $product->marketplace->action_name : Yii::t('XcoinModule.product', 'Buy this product'),
                                ['/xcoin/transaction/select-account', 'container' => Yii::$app->user->identity, 'productId' => $product->id],
                                ['class' => 'btn btn-sm btn-default pull-right', 'data-target' => '#globalModal', 'data-ui-loader' => '']
                            ); ?>
                        <?php else : ?>
                            <?php if ($product->marketplace->shouldRedirectToLink()): ?>
                                <?= Html::a(
                                    $product->marketplace->action_name ? $product->marketplace->action_name : Yii::t('XcoinModule.product', 'Buy this product'),
                                    $product->link,
                                    ['target' => '_blank']
                                ) ?>
                            <?php else : ?>
                                <?= Html::a(
                                    $product->marketplace->action_name ? $product->marketplace->action_name : Yii::t('XcoinModule.product', 'Buy this product'),
                                    ['/xcoin/product/buy', 'container' => Yii::$app->user->identity, 'productId' => $product->id],
                                    ['data-ui-loader' => true]
                                ) ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            <!-- product buy action end -->
        </div>
    </div>
</div>