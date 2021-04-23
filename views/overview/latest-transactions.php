<?php

use yii\bootstrap\Html;
use humhub\modules\xcoin\grids\LatestTransactionsGridView;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.overview', 'Back to overview'), ['/xcoin/overview', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>
        </div>

        <?php if ($this->context->contentContainer instanceof User): ?>
            <?= Yii::t('XcoinModule.overview', '<strong>Latest transactions</strong> of all user accounts') ?>
        <?php elseif ($this->context->contentContainer instanceof Space): ?>
            <?= Yii::t('XcoinModule.overview', '<strong>Latest transactions</strong> of all space owned accounts') ?>
        <?php endif; ?>
    </div>

    <div class="panel-body">
        <?= LatestTransactionsGridView::widget(['contentContainer' => $this->context->contentContainer]) ?>
    </div>
</div>