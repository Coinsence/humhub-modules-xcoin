<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Asset;
use yii\bootstrap\Html;
use humhub\modules\xcoin\grids\AccountsGridView;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\widgets\AssetDistribution;

/** @var Asset $asset */

Assets::register($this);
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.overview', 'Latest account transactions'), ['/xcoin/overview/latest-transactions', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>
            <?php if (AccountHelper::canCreateAccount($this->context->contentContainer)) : ?>
                <?= Html::a(Yii::t('XcoinModule.overview', 'Create account'), ['/xcoin/account/edit', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>

        <strong><?= Yii::t('XcoinModule.overview', 'Accounts of this space') ?></strong>
    </div>

    <div class="panel-body">
        <div class="pull-right">
            <h1 style="color: #bac2c7">
                <b>
                    <?= Yii::t('XcoinModule.overview', 'Total Issued Amount') ?> :
                    <?= $asset->getIssuedAmount() ?>
                </b>
            </h1>
        </div>
        
        <?= AccountsGridView::widget(['contentContainer' => $this->context->contentContainer]) ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.overview', 'Shareholder list'), ['/xcoin/overview/shareholder-list', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>
            <?= Html::a(Yii::t('XcoinModule.overview', 'Latest asset transactions'), ['/xcoin/overview/latest-asset-transactions', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>
            <?php if (AssetHelper::canManageAssets($this->context->contentContainer) && $asset !== null): ?>
                <?= Html::a(Yii::t('XcoinModule.overview', 'Issue new assets'), ['/xcoin/asset/issue', 'id' => $asset->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>
        <?= Yii::t('XcoinModule.overview', '<strong>Asset</strong> distribution') ?>
    </div>

    <div class="panel-body">
        <?= AssetDistribution::widget(['asset' => $asset]) ?>
    </div>
</div>
