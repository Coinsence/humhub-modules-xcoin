<?php

use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\widgets\AssetImage;
use yii\bootstrap\Html;

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
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <center>
                        <?= AssetImage::widget(['asset' => $funding->asset, 'width' => 38]); ?>
                        <br/>
                        <?= Html::encode($funding->asset->space->name); ?>
                    </center>
                    <hr/>
                </div>
                <div class="panel-body">

                    <center><strong>
                            <?=
                            Yii::t('XcoinModule.base', '{rate} for {spaceAsset}', [
                                'rate' => '1 ' . AssetImage::widget(['asset' => $funding->asset, 'width' => 24]),
                                'spaceAsset' => $funding->exchange_rate . ' ' . AssetImage::widget(['asset' => $myAsset, 'width' => 24]),
                            ])
                            ?>
                        </strong></center>
                    <br/>
                    <div class="pull-right" style="padding-top:12px">
                        <?=
                        Yii::t('XcoinModule.base', 'Max. {spaceAssetAmount} available.', [
                            'spaceAssetAmount' => $funding->getBaseMaximumAmount() . ' ' . AssetImage::widget(['asset' => $myAsset, 'width' => 16]),
                        ])
                        ?>
                    </div>
                    <br/>
                    <br/>
                    <hr>

                    <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                        <?= Html::a(Yii::t('base', 'Edit'), ['edit', 'id' => $funding->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm', 'data-target' => '#globalModal']); ?>
                    <?php endif; ?>
                    <div class="pull-right">
                        <?php if (Yii::$app->user->isGuest): ?>
                            <?= Html::a(Yii::t('XcoinModule.base', 'Invest'), Yii::$app->user->loginUrl, ['data-target' => '#globalModal', 'class' => 'btn btn-success btn']) ?>
                        <?php else: ?>
                            <?= Html::a(Yii::t('XcoinModule.base', 'Invest'), ['invest', 'fundingId' => $funding->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-success btn', 'data-target' => '#globalModal', 'disabled' => $funding->getBaseMaximumAmount() == 0]); ?>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    <?php endforeach; ?>


</div>