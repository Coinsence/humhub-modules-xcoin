<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\models\Product;
use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\helpers\Url;

Assets::register($this);

/** @var $products Product[] */
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-left status">
                <a <?= strpos(Yii::$app->request->url, '?verified') !== false ? 'class="active"' : '' ?> href="<?= Url::to(['/xcoin/marketplace', 'verified' => Product::PRODUCT_REVIEWED]) ?>"><?= Yii::t('XcoinModule.product', 'Verified') ?></a>
                <a <?= strpos(Yii::$app->request->url, '?verified') === false ? 'class="active"' : '' ?> href="<?= Url::to(['/xcoin/marketplace']) ?>"><?= Yii::t('XcoinModule.product', 'Under review') ?></a>
            </div>
            <div class="pull-right sell-button">
                <?= Html::a(Yii::t('XcoinModule.marketplace', 'Sell Product'), [
                    '/xcoin/marketplace/sell',
                ], ['class' => 'btn btn-gradient-1 btn-lg', 'data-target' => '#globalModal']); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 fundingPanels">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong><?= Yii::t('XcoinModule.marketplace', 'Marketplace') ?></strong>
                </div>
                <div class="panel-body">
                    <?php if (count($products) === 0): ?>
                        <div class="alert alert-default">
                            <?= Yii::t('XcoinModule.marketplace', 'Currently there are no products available!') ?>
                        </div>
                    <?php endif; ?>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <?php
                            $user = $product->getCreatedBy()->one();
                            $userProfile = $user->getProfile()->one();
                            $space = $product->getSpace()->one();
                            $picture = $product->getPicture();
                            $overviewLink = $product->isSpaceProduct() ? $space->createUrl('/xcoin/product/overview', [
                                'productId' => $product->id
                            ]) : $user->createUrl('/xcoin/product/overview', [
                                'productId' => $product->id
                            ]) ?>
                            <a href="<?= $overviewLink ?>">
                                <div class="col-md-3">
                                    <div class="panel">
                                        <div class="panel-heading">
                                            <!-- product picture start -->
                                            <?php if ($picture) : ?>
                                                <div class="bg" style="background-image: url('<?= $picture->getUrl() ?>')"></div>
                                                <?= Html::img($picture->getUrl(), ['height' => '140']) ?>
                                            <?php else : ?>
                                                <div class="bg" style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>')"></div>
                                                <img src="<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>"
                                                     height="140"/>
                                            <?php endif ?>

                                            <!-- product picture end -->
                                            <div class="project-owner">
                                                <?php if ($product->isSpaceProduct()) : ?>
                                                    <!-- space image start -->
                                                    <?= SpaceImage::widget([
                                                        'space' => $space,
                                                        'width' => 34,
                                                        'showTooltip' => true,
                                                        'link' => false
                                                    ]); ?>
                                                    <!-- space image end -->
                                                <?php else : ?>
                                                    <!-- user profile image start -->
                                                    <?= UserImage::widget([
                                                        'user' => $user,
                                                        'width' => 34,
                                                        'showTooltip' => true,
                                                        'link' => false,
                                                    ]); ?>
                                                    <!-- user profile image end -->
                                                <?php endif; ?>
                                                <!-- product name start -->
                                                <span>
                                            <?= $product->isSpaceProduct() ?
                                                "<strong>" . Html::encode($space->name) . "</strong>" :
                                                "<strong>" . Html::encode($userProfile->firstname) . " " . Html::encode($userProfile->lastname) . "</strong>"; ?>
                                                    <?php if ($product->review_status == Product::PRODUCT_NOT_REVIEWED) : ?>
                                                        <div style="color: orange; display: inline">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.product', 'Under review') ?>"></i>
                                                </div>
                                                    <?php else: ?>
                                                        <div style="color: dodgerblue; display: inline">
                                                    <i class="fa fa-check-circle-o" aria-hidden="true" rel="tooltip" title="<?= Yii::t('XcoinModule.product', 'Verified') ?>"></i>
                                                </div>
                                                    <?php endif; ?>
                                        </span>
                                                <!-- product name end -->
                                            </div>
                                        </div>
                                        <div class="panel-body">
                                            <h4 class="funding-title"><?= Html::encode($product->name); ?></h4>
                                            <div class="media">
                                                <div class="media-left media-middle">
                                                </div>
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
                                                                'space' => $product->asset->space,
                                                                'width' => 24,
                                                                'showTooltip' => true,
                                                                'link' => false
                                                            ]); ?>
                                                            <small> <?= $product->getPaymentType() ?> </small>
                                                        <?php else : ?>
                                                            <b><?= $product->discount ?>%</b>
                                                            <?= SpaceImage::widget([
                                                                'space' => $product->asset->space,
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
                <br/>
            </div>
        </div>
    </div>
</div>
