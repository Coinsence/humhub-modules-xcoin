<?php

use yii\bootstrap\Html;
use humhub\modules\xcoin\grids\AccountsGridView;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\widgets\AssetDistribution;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.base', 'Latest account transactions'), ['/xcoin/overview/latest-transactions', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>
            <?php if (AccountHelper::canCreateAccount($this->context->contentContainer)) : ?>
                <?= Html::a(Yii::t('XcoinModule.base', 'Create account'), ['/xcoin/account/edit', 'container' => $this->context->contentContainer], ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>

        <?= Yii::t('XcoinModule.base', '<strong>Accounts of this space</strong>'); ?>
    </div>

    <div class="panel-body">
        <?= AccountsGridView::widget(['contentContainer' => $this->context->contentContainer]) ?>
        <small><?= Yii::t('XcoinModule.base', 'If no manager is specified this account is manged by the owner or it\'s representatives.'); ?></small>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.base', 'Shareholder list'), ['/xcoin/overview/shareholder-list', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>
            <?= Html::a(Yii::t('XcoinModule.base', 'Latest asset transactions'), ['/xcoin/overview/latest-asset-transactions', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>
            <?php if (AssetHelper::canManageAssets($this->context->contentContainer) && $asset !== null): ?>
                <?= Html::a(Yii::t('XcoinModule.base', 'Issue new assets'), ['/xcoin/asset/issue', 'id' => $asset->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>
        <?= Yii::t('XcoinModule.base', '<strong>Asset</strong> distribution'); ?>
    </div>

    <div class="panel-body">
        <?= AssetDistribution::widget(['asset' => $asset]) ?>
    </div>
</div>
