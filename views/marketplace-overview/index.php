<?php

use humhub\libs\Iso3166Codes;
use humhub\modules\user\widgets\Image;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Marketplace;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\models\ProductFilter;
use humhub\widgets\ActiveForm;
use kv4nt\owlcarousel\OwlCarouselWidget;
use humhub\assets\Select2BootstrapAsset;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use \yii\helpers\Url;
use \yii\helpers\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\helpers\ArrayHelper;
use humhub\modules\xcoin\models\Category;

Assets::register($this);
Select2BootstrapAsset::register($this);

/** @var $selectedMarketplace Marketplace | null */
/** @var $products Product[] */
/** @var $assetsList array */
/** @var $marketplacesList array */
/** @var $countriesList array */
/** @var $marketplacesCarousel array */
/** @var $model ProductFilter */

?>

<style>
    .crowd-funding .content .add-project{
        height: 320px;
    }
</style>

<div class="crowd-funding">
    <div class="filters">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    OwlCarouselWidget::begin([
                        'container' => 'div',
                        'containerOptions' => [
                            'class' => 'categories'
                        ],
                        'pluginOptions' => [
                            'responsive' => [
                                0 => [
                                    'items' => 2
                                ],
                                520 => [
                                    'items' => 3
                                ],
                                768 => [
                                    'items' => 4
                                ],
                                1192 => [
                                    'items' => 5
                                ],
                                1366 => [
                                    'items' => 6
                                ],
                                1556 => [
                                    'items' => 8
                                ],
                            ],
                            'margin' => 10,
                            'nav' => true,
                            'dots' => false,
                            'navText' => ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>']
                        ]
                    ]);
                    ?>
                    <label class="category all">
                        <input type="radio" name="categroy" checked>
                        <a href="<?= Url::to(['/xcoin/marketplace-overview']) ?>"><span><label>All</label></span></a>
                    </label>
                    <?php foreach ($marketplacesCarousel as $marketplace): ?>
                        <label class="category">
                            <a href="<?= Url::to(['/xcoin/marketplace-overview', 'marketplaceId' => $marketplace['id']]) ?>">
                                <span style="background-image: url('<?= $marketplace['img'] ?>'); "><label><?= $marketplace['text'] ?></label></span>
                            </a>
                        </label>
                    <?php endforeach; ?>
                    <?php OwlCarouselWidget::end(); ?>
                </div>
            </div>
            <?php $form = ActiveForm::begin(['id' => 'filter-form']); ?>
            <div class="row">
                <?php if (!empty($assetsList)) : ?>
                    <div class="col-md-3 asset">
                        <?=
                        $form->field($model, 'asset_id')->widget(Select2::class, [
                            'data' => $assetsList,
                            'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.marketplace', 'Asset') . ' - ', 'value' => $model->asset_id ? $model->asset_id : []],
                            'theme' => Select2::THEME_BOOTSTRAP,
                            'hideSearch' => false,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                            ],
                        ])->label(false)
                        ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($marketplacesList)) : ?>
                    <div class="col-md-3 challenge">
                        <?=
                        $form->field($model, 'marketplace_id')->widget(Select2::class, [
                            'data' => $marketplacesList,
                            'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.marketplace', 'Marketplace') . ' - ', 'value' => []],
                            'theme' => Select2::THEME_BOOTSTRAP,
                            'hideSearch' => true,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                            ],
                        ])->label(false)
                        ?>
                    </div>
                <?php endif; ?>
                <div class="col-md-3">
                    <?= $form->field($model, 'categories')->widget(Select2::class, [
                        'data' => ArrayHelper::map(Category::find()->where(['type' => Category::TYPE_MARKETPLACE])->all(), 'id', 'name'),
                        'options' => [
                            'placeholder' => '- ' . Yii::t('XcoinModule.marketplace', 'Categories') . ' - ',
                            'multiple' => true,
                        ],
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                        ],
                    ])->label(false); ?>
                </div>
                <div class="col-md-3 location">
                    <div id="location-field" class="location-field">
                        <div class="location-selection">
                            <span class="selection-text">
                                <?php if ($model->country): ?>
                                    <?= iso3166Codes::country($model->country) ?>
                                <?php endif ?>
                                <?php if ($model->city && $model->country): ?>
                                    <?= Html::encode(' , ' . ucfirst($model->city)) ?>
                                <?php else : ?>
                                    <?= Html::encode(ucfirst($model->city)) ?>
                                <?php endif ?>
                                <?php if (!$model->country && !$model->city): ?>
                                    <?= Yii::t('XcoinModule.marketplace', 'Select location..') ?>
                                <?php endif ?>
                            </span>
                            <span class="selection-arrow">
                                <b></b>
                            </span>
                        </div>
                        <div class="location-dropdown">
                            <div class="dropdown-body">
                                <div class="row">
                                    <div class="col-md-6"><?= Yii::t('XcoinModule.marketplace', 'Country:') ?></div>
                                    <div class="col-md-6">
                                        <?=
                                        $form->field($model, 'country')->widget(Select2::class, [
                                            'data' => iso3166Codes::$countries,
                                            'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.marketplace', 'Select country') . ' - '],
                                            'theme' => Select2::THEME_BOOTSTRAP,
                                            'hideSearch' => false,
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                                            ],
                                        ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6"><?= Yii::t('XcoinModule.marketplace', 'City:') ?></div>
                                    <div class="col-md-6">
                                        <?=
                                        $form->field($model, 'city')->textInput([
                                            'placeholder' => Yii::t('XcoinModule.marketplace', 'Type your city name')
                                        ])->label(false)
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-footer">
                                <a class="reset-location" href="javascript:"><i class="fa fa-undo"></i> Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 keywords">
                    <?=
                    $form->field($model, 'keywords')->textInput([
                        'placeholder' => Yii::t('XcoinModule.marketplace', 'Search by keyword..')
                    ])->label(false)
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 filter-actions">
                    <?= Html::submitButton(Yii::t('XcoinModule.marketplace', 'Apply filter'), ['class' => 'sumbit btn btn-gradient-1']) ?>
                    <?= Html::Button(Yii::t('XcoinModule.marketplace', 'Reset'), ['class' => 'reset btn btn-default']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="content">
        <div class="container">
            <div class="row header">
                <?php if ($selectedMarketplace): ?>
                    <div class="col-md-12">
                        <a class="challenge-url" href="<?= $selectedMarketplace->space->createUrl('/xcoin/marketplace/overview', ['marketplaceId' => $selectedMarketplace->id]) ?>"><?= $selectedMarketplace->title ?></a>
                    </div>
                <?php endif; ?>
                <div class="col-md-6">
                    <span class="num-projects"><?= count($products) . ' ' . Yii::t('XcoinModule.marketplace', 'Product(s)') ?></span>
                </div>
            </div>
            <div class="panels">
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <a class="add-project" href="<?= Url::to(['/xcoin/marketplace-overview/new']) ?>"
                       data-target="#globalModal">
                        <span class="icon">
                            <i class="cross"></i>
                        </span>
                        <span class="text"><?= Yii::t('XcoinModule.marketplace', 'Sell Your Product!') ?></span>
                    </a>
                </div>
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
                                        <div class="project-owner">
                                            <!-- user image start -->
                                            <?= Image::widget([
                                                'user' => $product->getCreatedBy()->one(),
                                                'width' => 34,
                                                'showTooltip' => true,
                                                'link' => false
                                            ]); ?>
                                            <!-- user image end -->

                                            <!-- user name start -->
                                            <span><?= Yii::t('XcoinModule.product', 'Product by') . " <strong>" . Html::encode($product->getCreatedBy()->one()->username) . "</strong>"; ?></span>
                                            <!-- user name end -->
                                        </div>
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
                                                        <?= Yii::t('XcoinModule.product', 'Discount') ?>
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
