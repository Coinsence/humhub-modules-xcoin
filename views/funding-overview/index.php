<?php

use humhub\modules\file\models\File;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\Funding;
use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Progress;

?>

<div class="container">
    <div class="row">
        <div class="col-md-12 fundingPanels">

            <?php if (count($fundings) === 0): ?>
                <div class="panel">
                    <div class="panel-heading">
                        <?= Yii::t('XcoinModule.base', 'Crowd Funding'); ?>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-warning">
                            <?= Yii::t('XcoinModule.base', 'Currently there are no running crowd fundings!'); ?>
                        </div>
                    </div>
                    <br/>
                </div>
            <?php endif; ?>

            <div class="row">
                <?php foreach ($fundings as $funding): ?>
                    <?php if ($funding->getBaseMaximumAmount() > 0 && $funding->getRemainingDays() > 0): ?>
                        <?php
                        $space = Space::findOne(['id' => $funding->space_id]);
                        $cover = File::find()->where(['object_model' => Funding::class, 'object_id' => $funding->id])->orderBy(['id' => SORT_ASC])->one();
                        ?>

                        <div class="col-md-4">
                            <div class="panel">
                                <div class="panel-heading">
                                    <!-- campaign cover start -->
                                    <?php if ($cover) : ?>
                                        <?= Html::img($cover->getUrl(), ['height' => '190']) ?>
                                    <?php else : ?>
                                        <?= Html::img('https://www.bbsocal.com/wp-content/uploads/2017/07/Funding-icon.jpg', ['height' => '190', 'width' => '320']) ?>
                                    <?php endif ?>
                                    <!-- campaign cover end -->

                                    <!-- space image start -->
                                    <?= SpaceImage::widget(['space' => $space, 'width' => 32, 'showTooltip' => true, 'link' => true]); ?>
                                    <!-- space image end -->

                                    <br>

                                    <!-- campaign title start -->
                                    <?= Html::encode($funding->title); ?>
                                    <!-- campaign title end -->

                                    <!-- campaign overview link start -->
                                    <?= Html::a('Invest', $space->createUrl('/xcoin/funding'), ['class' => 'btn btn-default pull-right']); ?>
                                    <!-- campaign overview link end -->
                                </div>
                                <div class="panel-body">
                                    <div class="media">
                                        <div class="media-left media-middle">
                                        </div>
                                        <div class="media-body">
                                            <!-- campaign description start -->
                                            <h4 class="media-heading"><?= Html::encode($funding->shortenDescription()); ?></h4>
                                            <!-- campaign description end -->
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-footer">
                                    <!-- campaign requesting start -->
                                    Requesting :
                                    <?= $funding->getRequestedAmount() ?>
                                    <?= SpaceImage::widget(['space' => $funding->asset->space, 'width' => 24, 'showTooltip' => true, 'link' => true]); ?>
                                    <!-- campaign requesting end -->

                                    <!-- campaign raised start -->
                                    <div class="pull-right">
                                        Raised : <?= $funding->getRaisedAmount() ?>
                                        (<?= $funding->getRaisedPercentage() ?>%)
                                    </div>
                                    <!-- campaign raised end -->

                                    <br><br>

                                    <!-- campaign raised start -->
                                    <?php echo Progress::widget([
                                        'percent' => $funding->getRaisedPercentage(),
                                    ]); ?>

                                    <!-- campaign offering start -->
                                    Offering : <?= $funding->getOfferedAmountPercentage() ?>
                                    % <?= SpaceImage::widget(['space' => $funding->space, 'width' => 24, 'showTooltip' => true, 'link' => true]); ?>
                                    <!-- campaign offering end -->

                                    <br>

                                    <!-- campaign space name start -->
                                    <?= Html::encode($space->name); ?>
                                    <!-- campaign space name end -->

                                    <br>

                                    <!-- campaign remaining days start -->
                                    <?= $funding->getRemainingDays() ?> <?= $funding->getRemainingDays() > 1 ? 'Days' : 'Day' ?> left
                                    <!-- campaign remaining days end -->

                                </div>
                            </div>
                        </div>

                    <?php endif; ?>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</div>

<style>
    .fundingPanels .panel-body {
        height: 80px;
    }

    .fundingPanels .panel-footer {
        height: 150px;
    }
</style>
