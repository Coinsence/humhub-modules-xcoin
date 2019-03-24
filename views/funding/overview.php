<?php

use humhub\modules\content\widgets\richtext\RichText;
use humhub\modules\file\models\File;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\Funding;
use yii\bootstrap\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Progress;

/**
 * @var $funding Funding
 */
?>

<div class="container">
    <div class="row">
        <div class="col-md-9 fundingPanels">
            <div class="row">
                <?php
                $space = Space::findOne(['id' => $funding->space_id]);
                $cover = File::find()->where(['object_model' => Funding::class, 'object_id' => $funding->id])->orderBy(['id' => SORT_ASC])->one();
                $gallery = File::find()->where(['object_model' => Funding::class, 'object_id' => $funding->id])->orderBy(['id' => SORT_DESC])->all();
                ?>

                <div class="col-md-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <!-- campaign cover start -->
                            <?php if ($cover) : ?>
                                <?= Html::img($cover->getUrl(), ['height' => '190']) ?>
                            <?php else : ?>
                                <?= Html::img('https://www.bbsocal.com/wp-content/uploads/2017/07/Funding-icon.jpg', ['height' => '190', 'width' => '320']) ?>
                            <?php endif ?>
                            <div class="pull-right">
                                <!-- campaign cover end -->
                                <br>
                                <!-- space image start -->
                                <?= SpaceImage::widget(['space' => $space, 'width' => 32, 'showTooltip' => true, 'link' => true]); ?>
                                <!-- space image end -->

                                <br>

                                <!-- campaign title start -->
                                <?= Html::encode($funding->title); ?>
                                <!-- campaign title end -->

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
                                <?= $funding->getRemainingDays() ?> <?= $funding->getRemainingDays() > 1 ? 'Days' : 'Day' ?>
                                left
                                <!-- campaign remaining days end -->

                                <!-- campaign invest action start -->
                                <div class="pull-right">
                                    <?php if (Yii::$app->user->isGuest): ?>
                                        <?= Html::a(Yii::t('XcoinModule.base', 'Invest'), Yii::$app->user->loginUrl, ['data-target' => '#globalModal', 'class' => 'btn btn-success btn']) ?>
                                    <?php else: ?>
                                        <?= Html::a(Yii::t('XcoinModule.base', 'Invest'), ['invest', 'fundingId' => $funding->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-success btn', 'data-target' => '#globalModal', 'disabled' => $funding->canInvest()]); ?>
                                    <?php endif; ?>
                                </div>
                                <!-- campaign invest action end -->
                            </div>
                        </div>
                        <div class="panel-body">

                            <!-- campaign description start -->
                            <h5 class="media-heading"><?= Html::encode($funding->description); ?></h5>
                            <!-- campaign description end -->
                        </div>
                        <div class="panel-footer">

                            <!-- campaign gallery start -->
                            <div class="row">
                                <?php foreach ($gallery as $item): ?>
                                    <div class="col-md-4">
                                        <?= Html::img($item->getUrl(), ['height' => '130']) ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- campaign gallery end -->
                            <br>

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

<style>
    .fundingPanels .panel-body {
        height: auto;
    }

    .fundingPanels .panel-footer {
        height: auto;
    }
</style>
