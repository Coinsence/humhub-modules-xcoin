<?php

use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;


use humhub\modules\user\widgets\Image;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Product;
use yii\bootstrap\Carousel;
use humhub\libs\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\libs\Iso3166Codes;
use humhub\modules\content\widgets\richtext\RichText;


Assets::register($this);

/**
 * @var $product Product
 */
?>

<div class="product-details-popup" id="product-popup">
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
<?php ModalDialog::begin(['header' => Html::encode($product->name), 'closable' => false]) ?>
    <?php $form = ActiveForm::begin(['id' => 'product-details']); ?>
    <div class="modal-container">
        <div class="modal-heading">
            <div class="modal-subtitle">
                <span class="text"><?= Html::encode($product->description); ?></span>
            </div>
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
                            'width' => '100%',

                        ]) ?>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="bg"
                            style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png' ?>')"></div>
                    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png', [
                        'width' => '100%',

                    ]) ?>
                <?php endif ?>
            </div>
        </div>

        <div class="modal-info row">
            <div class="col-md-8">
                <div class="text-content">
                    <span class="text-heading"><?= Yii::t('XcoinModule.product', 'About the product') ?></span>
                    <div class="product-content">
                        <?= RichText::output($product->content); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="side-widget">
                    <div class="info">
                        <label><?= Yii::t('XcoinModule.product', 'Created by') ?></label>
                        <span>
                            <?php if ($product->isSpaceProduct()): ?>
                                <?= SpaceImage::widget([
                                    'space' => $product->getSpace()->one(),
                                    'width' => 24,
                                    'showTooltip' => false,
                                    'link' => true
                                ]); ?>
                                <?= " <strong>" . Html::encode($product->getSpace()->one()->name) . "</strong>"; ?>
                            <?php else : ?>
                                <?= Image::widget([
                                    'user' => $product->getCreatedBy()->one(),
                                    'width' => 24,
                                    'showTooltip' => false,
                                    'link' => true
                                ]); ?>
                                <?= " <strong>" . Html::encode($product->getCreatedBy()->one()->profile->firstname . " " . $product->getCreatedBy()->one()->profile->lastname) . "</strong>"; ?>
                            <?php endif; ?>
                        </span>

                        <label>
                            <?= Yii::t('XcoinModule.product', 'Price') ?>
                            <?php if ($product->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS) : ?>
                                <small> <?= $product->getPaymentType() ?> </small>
                            <?php endif; ?>
                        </label>
                        <span class="price">
                            <?php if ($product->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS) : ?>
                                <strong><?= $product->price ?></strong>
                                <?= SpaceImage::widget([
                                    'space' => $product->marketplace->asset->space,
                                    'width' => 24,
                                    'showTooltip' => true,
                                    'link' => true
                                ]); ?>
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
                        </span>

                        <label><?= Yii::t('XcoinModule.product', 'Location') ?></label>
                        <span><strong><?= Iso3166Codes::country($product->country) . ', ' . $product->city ?></strong></span>

                        <label><?= Yii::t('XcoinModule.product', 'Category') ?></label>
                        <span>
                            <?= join(', ', array_map(function($cat) {
                                return '<strong>' . $cat->name . '</strong>';
                            }, $product->getCategories()->all())) ?>
                        </span>
                    </div>
                    <hr/>
                    <div class="actions">
                        <!-- product buy action start -->
                        <div class="invest-btn <?= ($product->status == Product::STATUS_UNAVAILABLE || $product->isOwner(Yii::$app->user->identity)) ? 'disabled' : '' ?>">
                            <?php if (Yii::$app->user->isGuest): ?>
                                <?= Html::a(Yii::t('XcoinModule.product', 'Buy this product'), Yii::$app->user->loginUrl, ['data-target' => '#globalModal']) ?>
                            <?php else: ?>
                                <?php if ($product->isPaymentFirst()) : ?>
                                    <?= Html::a(
                                        $product->marketplace->action_name ? $product->marketplace->action_name : Yii::t('XcoinModule.product', 'Buy this product'),
                                        ['/xcoin/transaction/select-account', 'container' => Yii::$app->user->identity, 'productId' => $product->id],
                                        ['data-target' => '#globalModal', 'data-ui-loader' => '']
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
                                            ['data-ui-loader' => true, 'class' => 'js-buy-product']
                                        ) ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                        <!-- product buy action end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
