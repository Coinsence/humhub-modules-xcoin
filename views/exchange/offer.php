<?php

use humhub\assets\Select2BootstrapAsset;
use humhub\libs\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\widgets\AmountField;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use kartik\widgets\Select2;
use yii\web\JsExpression;

Select2BootstrapAsset::register($this);
?>
<?php ModalDialog::begin(['header' => '<strong>Exchange</strong> asset', 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>
<div class="modal-body">
    <div class="form-group">
        <label class="control-label">Sender account</label>
        <div class="form-control" style="padding-top:4px;">
            <?php if ($fromAccount->space !== null): ?>
                <?= SpaceImage::widget(['space' => $fromAccount->space, 'width' => 24]); ?>
            <?php endif; ?>
            <?php if ($fromAccount->user !== null): ?>
                <?= UserImage::widget(['user' => $fromAccount->user, 'width' => 24]); ?>
            <?php endif; ?>
            <?= $fromAccount->title; ?>
            <?= Html::a('Change', ['/xcoin/exchange/offer'], ['class' => 'btn btn-sm btn-default pull-right', 'style' => 'margin-top:-2px;margin-right:-10px', 'data-target' => '#globalModal', 'data-ui-loader' => '']); ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6"><?=
            $form->field($exchange, 'asset_id')->widget(Select2::class, [
                'data' => $accountAssetList,
                'options' => ['placeholder' => '- Select asset - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ]);
            ?></div>
        <div class="col-md-6"><?= $form->field($exchange, 'available_amount')->label('Total amount'); ?></div>
    </div>

    <div class="row blockDefineExchangeRate">
        <div class="col-md-6">
            <?=
            $form->field($exchange, 'wanted_asset_id')->widget(Select2::classname(), [
                'data' => $assetList,
                'options' => ['placeholder' => '- Select asset - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($exchange, 'exchange_rate')->textInput()->label('Price per unit'); ?>
        </div>
    </div>


    <script>
        /*
        $('.blockDefineExchangeRate').hide();
        $("#exchange-asset_id").on("select2:select", function(e) {
            showRateOptions();
        });

        function showRateOptions() {
            $('.blockDefineExchangeRate').show();

            // Transfer asset icon to price field
            var html = $('#exchange-asset_id').select2('data')[0].text;
            $('.field-exchange-exchange_rate').find('.input-group-addon').empty().append($(html)[0]).append($(html)[1]);
        }
        */
    </script>


    <hr/>

</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
