<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\grids\LatestTransactionsGridView;
use humhub\modules\xcoin\grids\AccountsGridView;
use humhub\modules\xcoin\widgets\CashoutCoinButton;
use humhub\modules\xcoin\widgets\PurchaseCoin;

Assets::register($this);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php if ($isOwner): ?>
            <strong><?= Yii::t('XcoinModule.overview', 'Your accounts') ?></strong>
            <?= PurchaseCoin::widget(['style' => 'float: right; margin-bottom: 12px;']) ?>
            <?= CashoutCoinButton::widget(['style' => 'float: right; margin: 0 10px 12px 0;']) ?>
        <?php else: ?>
            <strong><?= Yii::t('XcoinModule.overview', 'Accounts of this user') ?></strong>
        <?php endif; ?>
    </div>

    <div class="panel-body">
        <?= AccountsGridView::widget(['contentContainer' => $this->context->contentContainer]) ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <?php if ($isOwner): ?>
            <?= Yii::t('XcoinModule.overview', '<strong>Latest</strong> transactions in your accounts') ?>
        <?php else: ?>
            <?= Yii::t('XcoinModule.overview', '<strong>Latest</strong> transactions') ?>
        <?php endif; ?>
    </div>

    <div class="panel-body">
        <?= LatestTransactionsGridView::widget(['contentContainer' => $this->context->contentContainer]) ?>
    </div>
</div>
