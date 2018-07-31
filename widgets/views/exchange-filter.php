<?php

use kartik\select2\Select2;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use humhub\assets\Select2BootstrapAsset;

Select2BootstrapAsset::register($this);
?>
<div class="panel">
    <div class="panel-heading">Filter</div>
    <div class="panel-body">
        <?= Html::beginForm(Url::to(['/xcoin/exchange']), 'get'); ?>

        <div class="form-group">
            <label>
                <?= Html::checkbox('filter-mine', $filters['mine'], ['class' => 'filterCheck']); ?>
                Show my offers only 
            </label>
        </div>


        <label>Offered Asset</label>
        <?=
        Select2::widget([
            'name' => 'filter-from',
            'data' => $assetList,
            'value' => $filters['from'],
            'options' => ['placeholder' => 'Show all assets', 'class' => 'filterField'],
            'theme' => Select2::THEME_BOOTSTRAP,
            'hideSearch' => true,
            'pluginOptions' => [
                'allowClear' => true,
                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
            ],
        ]);
        ?>
        <br />
        <br />
        <label>Requested Asset</label>
        <?=
        Select2::widget([
            'name' => 'filter-to',
            'data' => $assetList,
            'value' => $filters['to'],
            'options' => ['placeholder' => 'Show all assets', 'class' => 'filterField'],
            'theme' => Select2::THEME_BOOTSTRAP,
            'hideSearch' => true,
            'pluginOptions' => [
                'allowClear' => true,
                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
            ],
        ]);
        ?>

        <?= Html::endForm(); ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('.filterField').change(function () {
            $(this).parents().filter("form").submit();
        });

        $('.filterCheck').change(function () {
            $(this).parents().filter("form").submit();
        });
    });
</script>

