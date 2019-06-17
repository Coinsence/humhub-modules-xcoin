<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Funding;
use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Progress;

Assets::register($this);

/**
 * @var $funding Funding
 */
?>

<div class="container">
    <div class="row">
        <div class="col-md-9 fundingPanel">
            <div class="row">
                <?php
                $cover = $funding->getCover();
                $gallery = $funding->getGallery();
                ?>

                <div class="col-md-12">
                    <div class="panel cover">
                        <div class="panel-heading">
                            <!-- campaign cover start -->
                            <div class="img-container">

                                <?php if ($cover) : ?>
                                    <div class="bg" style="background-image: url('<?= $cover->getUrl() ?>')"></div>
                                    <?= Html::img($cover->getUrl(), ['width' => '100%']) ?>
                                <?php else : ?>
                                    <div class="bg" style="background-image: url('<?= Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png' ?>')"></div>
                                    <?= Html::img(Yii::$app->getModule('xcoin')->getAssetsUrl() . '/images/default-funding-cover.png', [
                                        'width' => '100%'
                                    ]) ?>
                                <?php endif ?>

                            </div>
                            <!-- campaign cover end -->
                            <!-- campaign invest action start -->
                            <?php if (!$funding->canInvest()): ?>
                            <div class="invest-btn disabled">
                                <?php else: ?>
                                <div class="invest-btn">
                                    <?php endif; ?>
                                    <?php if (Yii::$app->user->isGuest): ?>
                                        <?= Html::a(Yii::t('XcoinModule.funding', 'Invest in this project'), Yii::$app->user->loginUrl, ['data-target' => '#globalModal']) ?>
                                    <?php else: ?>
                                        <?= Html::a(Yii::t('XcoinModule.funding', 'Invest in this project'), [
                                            'invest',
                                            'fundingId' => $funding->id,
                                            'container' => $this->context->contentContainer
                                        ], ['data-target' => '#globalModal']); ?>
                                    <?php endif; ?>

                                </div>
                                <!-- campaign invest action end -->
                                <!-- campaign edit button start -->
                                <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                                    <?= Html::a('<i class="fa fa-pencil"></i>' . Yii::t('XcoinModule.funding', 'Edit'), ['/xcoin/funding/edit', 'id' => $funding->id, 'container' => $this->context->contentContainer], ['data-target' => '#globalModal', 'class' => 'edit-btn']) ?>
                                <?php endif; ?>
                                <!-- campaign edit button end -->

                            </div>
                            <div class="panel-body">

                                <!-- campaign title start -->
                                <h4 class="funding-title"><?= Html::encode($funding->title); ?></h4>
                                <!-- campaign title end -->

                                <div class="row">
                                    <div class="col-md-6">
                                        <!-- campaign description start -->
                                        <p class="media-heading"><?= Html::encode($funding->description); ?></p>
                                        <!-- campaign description end -->
                                    </div>
                                    <div class="col-md-3"></div>
                                    <div class="col-md-3">

                                        <div class="col-md-12 funding-details">
                                            <!-- campaign requesting start -->
                                            <?= Yii::t('XcoinModule.funding', 'Requesting:') ?>
                                            <strong><?= $funding->getRequestedAmount() ?></strong>
                                            <?= SpaceImage::widget([
                                                'space' => $funding->asset->space,
                                                'width' => 24,
                                                'showTooltip' => true,
                                                'link' => true
                                            ]); ?>
                                            <!-- campaign requesting end -->
                                        </div>
                                        <div class="col-md-12 funding-details">
                                            <!-- campaign offering start -->
                                            <?= Yii::t('XcoinModule.funding', 'Offering:') ?>
                                            <strong><?= $funding->getOfferedAmountPercentage() ?></strong>%
                                            <?= SpaceImage::widget([
                                                'space' => $funding->space,
                                                'width' => 24,
                                                'showTooltip' => true,
                                                'link' => true
                                            ]); ?>
                                            <!-- campaign offering end -->
                                        </div>


                                    </div>
                                </div>


                            </div>
                            <div class="panel-footer">

                                <div class="funding-progress">

                                    <div>
                                        <!-- campaign raised start -->
                                        <?= Yii::t('XcoinModule.funding', 'Raised:') ?> <strong><?= $funding->getRaisedAmount() ?></strong>
                                        (<strong><?= $funding->getRaisedPercentage() ?>%</strong>)
                                        <!-- campaign raised end -->
                                    </div>

                                    <div class="pull-right">

                                        <!-- campaign remaining days start -->
                                        <?php if ($funding->getRemainingDays() > 2) : ?>
                                            <div class="clock"></div>
                                        <?php else: ?>
                                            <div class="clock red"></div>
                                        <?php endif; ?>
                                        <div class="days">
                                            <?php if ($funding->getRemainingDays() > 0) : ?>
                                                <strong><?= $funding->getRemainingDays() ?></strong> <?= $funding->getRemainingDays() > 1 ? Yii::t('XcoinModule.funding', 'Days left') : Yii::t('XcoinModule.funding', 'Day left') ?>
                                            <?php else : ?>
                                                <strong><?= Yii::t('XcoinModule.funding', 'Closed') ?></strong>
                                            <?php endif; ?>
                                        </div>
                                        <!-- campaign remaining days end -->

                                    </div>

                                    <!-- campaign raised start -->
                                    <?php echo Progress::widget([
                                        'percent' => $funding->getRaisedPercentage(),
                                    ]); ?>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="panel content">
                            <div class="panel-heading">
                                <!-- campaign gallery start -->
                                <div class="row">
                                    <?php foreach ($gallery as $item): ?>
                                        <div class="col-md-4">
                                            <?= Html::img($item->getUrl(), ['height' => '130']) ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <!-- campaign gallery end -->
                            </div>
                            <div class="panel-body">

                                <!-- campaign content start -->
                                <?= RichText::output($funding->content); ?>
                                <!-- campaign content end -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
