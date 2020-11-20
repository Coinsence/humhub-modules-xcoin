<?php

use humhub\assets\Select2BootstrapAsset;
use humhub\modules\space\widgets\Image as SpaceImage;


// test begin
use humhub\modules\user\widgets\ProfileHeader;
use humhub\modules\user\widgets\ProjectPortfolio;
use humhub\modules\user\widgets\ProfileMenu;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Challenge;
use humhub\widgets\FooterMenu;
use yii\bootstrap\Progress;
use \humhub\modules\xcoin\models\Funding;
use \yii\helpers\Html;
use \yii\helpers\Url;
use humhub\modules\xcoin\models\Product;

Assets::register($this);
Select2BootstrapAsset::register($this);
/** @var $selectedChallenge Challenge | null */
/** @var $products Product[] */
/** @var $assetsList array */
/** @var $challengesList array */
/** @var $countriesList array */
/** @var $challengesCarousel array */
/** @var $isProfileOwner array */

?>

<div class="content">
    <div class="marketPlacePortfolio">
        <!-- <div class="row header">
                <?/*php if ($selectedMarketplace): */?>
                <div class="col-md-12">
                    <a class="challenge-url"
                        href="<?/*= $selectedMarketplace->space->createUrl('/xcoin/marketplace/overview', ['marketplaceId' => $selectedMarketplace->id]) */?>"><?/*= $selectedMarketplace->title */?></a>
                </div>
                <?/*php endif; */?>
                <div class="col-md-6">
                    <span
                        class="num-projects"><?/*= count($products) . ' ' . Yii::t('XcoinModule.marketplace', 'Product(s)') */?></span>
                </div>
            </div> -->
        <div class="headerMarketPlace">
            <h2>Marketplace Portfolio</h2>
            <div class="arrows"></div>
        </div>
        <div class="panels marketPlacesSlider">

            <?php if ($isProfileOwner) : ?>
            <div class="marketPlaceCard createNewMarketPlace">
                <a class="add-project" href="<?= Url::to(['/xcoin/marketplace-overview/new']) ?>"
                    data-target="#globalModal">
                    <span class="addMarketPlaceCross">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span
                        class="addMarketPlaceText"><?= Yii::t('XcoinModule.marketplace', 'Sell Your Product!') ?></span>
                </a>
            </div>
            <?php endif ?>
            <?php foreach ($products as $product): ?>
            <?php
                        $space = $product->getSpace()->one();
                        $picture = $product->getPicture();
                        ?>
            <a href="">
                <!-- begin test -->

                <div class="marketPlaceCard">
                    <div class="marketPlaceCardHeader">
                        <!-- product picture start -->
                        <?php if ($picture) : ?>
                        <div class="bg" style="background-image: url('<?= $picture->getUrl() ?>')"></div>
                        <?= Html::img($picture->getUrl(), ['height' => '140']) ?>
                        <?php else : ?>
                        <div class="bg"
                            style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png' ?>')">
                        </div>
                        <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-product-cover.png', [
                                                'height' => '140',
                                                'width' => '320'
                                            ]) ?>
                        <?php endif ?>
                        <!-- product picture end -->
                    </div>
                    <div class="marketPlaceCardBody">
                        <img class="marketPlaceImage" src="./img/projectLogo2.png" alt="" />
                        <h2 class="marketPlaceName">
                            <?= Html::encode($product->name); ?>
                            <?php if ($product->review_status == Product::PRODUCT_NOT_REVIEWED) : ?>
                            <div style="color: orange; display: inline">
                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                    title="<?= Yii::t('XcoinModule.product', 'Under review') ?>"></i>
                            </div>
                            <?php else: ?>
                            <div style="color: dodgerblue; display: inline">
                                <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip"
                                    title="<?= Yii::t('XcoinModule.product', 'Verified') ?>"></i>
                            </div>
                            <?php endif; ?>
                        </h2>
                        <p class="description">
                            <?= Html::encode($product->shortenDescription()); ?>
                        </p>
                    </div>
                    <div class="marketPlaceCardFooter">
                        <!-- <b class="discount">50% Discount</b>
                            <img class="coinImage" src="./img/coinsenceToken.jpg" alt="coin" /> -->
                        <?php if ($product->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS) : ?>
                        <span><?= Yii::t('XcoinModule.product', 'Price') ?> :
                            <?= $product->price ?></span>
                        <?= SpaceImage::widget([
                                                            'space' => $product->marketplace->asset->space,
                                                            'width' => 24,
                                                            'showTooltip' => true,
                                                            'link' => false
                                                        ]); ?>
                        <small> <?= $product->getPaymentType() ?> </small>
                        <?php else : ?>
                        <span><?= $product->discount ?></span> %
                        <?= SpaceImage::widget([
                                                            'space' => $product->marketplace->asset->space,
                                                            'width' => 24,
                                                            'showTooltip' => true,
                                                            'link' => false
                                                        ]); ?>
                        <span><?= Yii::t('XcoinModule.product', 'Discount') ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- end test -->
            </a>
            <?php endforeach; ?>

        </div>
    </div>
</div>
<script>
$(".marketPlacesSlider").slick({
    infinite: false,
    slidesToShow: 1,
    variableWidth: true,
    appendArrows: $(".marketPlacePortfolio .arrows"),

});

$(".slick-prev").append('<i class="fas fa-angle-left"></i>');
$(".slick-next").append('<i class="fas fa-angle-right"></i>');
</script>
<script src="themes/Coinsence/js/sliders.js"></script>