<?php

use humhub\modules\file\models\File;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\bootstrap\Html;
use yii\bootstrap\Progress;

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                <?= Html::a(Yii::t('XcoinModule.base', 'Add asset offer'), ['/xcoin/funding/edit', 'container' => $this->context->contentContainer], ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>
        <?= Yii::t('XcoinModule.base', '<strong>Crowd</strong> funding'); ?>
    </div>

    <div class="panel-body">
        <p><?= Yii::t('XcoinModule.base', 'The assets listed below are currently wanted as crowd funding investment.'); ?></p>

        <?php if (count($activeFundings) === 0): ?>
            <br/>
            <p class="alert alert-warning">
                <?= Yii::t('XcoinModule.base', 'Currently there are no open funding requests.'); ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <?php foreach ($fundings as $funding): ?>
            <?php
            $space = Space::findOne(['id' => $funding->space_id]);
            $cover = File::find()->where(['object_model' => Funding::class, 'object_id' => $funding->id])->orderBy(['id' => SORT_ASC])->one();
            ?>

            <div class="col-md-4">
                <div class="panel">
                    <div class="panel-heading">
                        <!-- campaign cover start -->
                        <?php if ($cover) : ?>
                            <?= Html::img($cover->getUrl(), ['height' => '150']) ?>
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

                        <!-- campaign edit button start -->
                        <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                            <?= Html::a(Yii::t('base', 'Edit'), ['edit', 'id' => $funding->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm', 'data-target' => '#globalModal']); ?>
                        <?php endif; ?>
                        <!-- campaign edit button end -->
                    </div>
                </div>
            </div>

    <?php endforeach; ?>
</div>
