<?php

use yii\bootstrap\Html;
use humhub\modules\xcoin\grids\LatestTransactionsGridView;
use humhub\modules\xcoin\grids\ShareholderGridView;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.overview', 'Back to overview'), ['/xcoin/overview', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>
        </div>

        <?= Yii::t('XcoinModule.overview', '<strong>Shareholder</strong> listing') ?>
    </div>

    <div class="panel-body">
        <?= ShareholderGridView::widget(['asset' => $asset]) ?>
    </div>
</div>