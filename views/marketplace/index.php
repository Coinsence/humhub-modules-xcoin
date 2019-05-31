<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\models\Product;
use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;

Assets::register($this);

/** @var $products Product[] */
?>

<div class="container">
    <div class="row">
        <div class="pull-right sell-button">
            <?= Html::a(Yii::t('XcoinModule.marketplace', 'Sell Product'), [
                '/xcoin/marketplace/sell',
            ], ['class' => 'btn btn-success btn-lg', 'data-target' => '#globalModal']); ?>
        </div>
        <div class="col-md-12 fundingPanels">
            <?php if (count($products) === 0): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <?= Yii::t('XcoinModule.base', '<strong>Marketplace</strong>'); ?>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-warning">
                            <?= Yii::t('XcoinModule.base', 'Currently there are no products available!'); ?>
                        </div>
                    </div>
                    <br/>
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
                                                    <?= Yii::t('XcoinModule.base', 'Price'); ?> : <b><?= $product->price ?></b>
                                                    <small> <?= $product->getPaymentType() ?> </small>
                                                <?php else : ?>
                                                    <?= $product->discount ?> % Discount
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
