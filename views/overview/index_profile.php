<?php

use humhub\modules\xcoin\assets\Assets;
use yii\bootstrap\Html;
use humhub\modules\xcoin\grids\LatestTransactionsGridView;
use humhub\modules\xcoin\grids\AccountsGridView;
use humhub\modules\xcoin\helpers\AccountHelper;

Assets::register($this);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <!--<?= Html::a(Yii::t('XcoinModule.base', 'Latest transactions'), ['/xcoin/overview/latest-transactions', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>-->
            <?php if (AccountHelper::canCreateAccount($this->context->contentContainer)) : ?>
                <?= Html::a(Yii::t('XcoinModule.base', 'Create account'), ['/xcoin/account/edit', 'container' => $this->context->contentContainer], ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>

        <?php if ($isOwner): ?>
            <?= Yii::t('XcoinModule.base', '<strong>Your accounts</strong>'); ?>
        <?php else: ?>
            <?= Yii::t('XcoinModule.base', '<strong>Accounts of this user</strong>'); ?>
        <?php endif; ?>
    </div>

    <div class="panel-body">
        <?= AccountsGridView::widget(['contentContainer' => $this->context->contentContainer]) ?>
        <small><?= Yii::t('XcoinModule.base', 'If no owner is specified, it is a personal account.'); ?></small>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php if ($isOwner): ?>
            <?= Yii::t('XcoinModule.base', '<strong>Latest</strong> transactions in your accounts'); ?>
        <?php else: ?>
            <?= Yii::t('XcoinModule.base', '<strong>Latest</strong> transactions'); ?>
        <?php endif; ?>
    </div>

    <div class="panel-body">
        <?= LatestTransactionsGridView::widget(['contentContainer' => $this->context->contentContainer]) ?>
    </div>
</div>

