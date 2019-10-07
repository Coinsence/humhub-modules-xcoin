<?php

use humhub\modules\xcoin\grids\ExchangeGridView;
use yii\bootstrap\Html;

?>
<div class="container">
    <div class="row">
        <div class="col-md-9">
            <div class="panel">
                <div class="panel-heading">
                    <?php if (!Yii::$app->user->isGuest): ?>
                        <?= Html::a(Yii::t('XcoinModule.exchange', 'Add new offer'), ['/xcoin/exchange/offer'], ['class' => 'btn btn-default pull-right', 'data-target' => '#globalModal']); ?>
                    <?php endif; ?>
                    <?= Yii::t('XcoinModule.exchange', 'Asset Exchange') ?>
                </div>
                <div class="panel-body">
                    <?= ExchangeGridView::widget(['query' => $query]); ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <?= humhub\modules\xcoin\widgets\ExchangeFilter::widget(); ?>
        </div>
    </div>
</div>
