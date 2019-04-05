<?php

use yii\bootstrap\Html;

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-heading">
                    Marketplace
                    <div class="pull-right">
                            <?= Html::a('Sell Product', [
                                '/xcoin/product/create',
                            ], ['class' => 'btn btn-success btn-sm', 'data-target' => '#globalModal']); ?>
                    </div>
                </div>
                <div class="panel-body">
                    ToDo: A simple list of items which are offered by users.
                </div>
            </div>
        </div>
    </div>
</div>
