<?php

use yii\bootstrap\Html;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\grids\LatestAssetTransactionsGridView;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.overview', 'Back to overview'), ['/xcoin/overview', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>
        </div>

        <?= Yii::t('XcoinModule.overview', '<strong>Latest transactions</strong> of space assets') ?>
    </div>

    <div class="panel-body">
        <?= LatestAssetTransactionsGridView::widget(['contentContainer' => $this->context->contentContainer, 'asset' => $asset]) ?>
    </div>
</div>