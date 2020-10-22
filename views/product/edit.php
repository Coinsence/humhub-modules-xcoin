<?php

use humhub\libs\Iso3166Codes;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Product;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use yii\web\JsExpression;
use humhub\modules\file\widgets\Upload;

/** @var $model Product */
/** @var $myAsset Asset */

$upload = Upload::withName();
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.product', 'Provide details'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'product-form']); ?>

<?= Html::hiddenInput('step', '2'); ?>

<?= $form->field($model, 'marketplace_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?=
            $form->field($model, 'status')->widget(Select2::class, [
                'data' => [
                    Product::STATUS_UNAVAILABLE => 'UNAVAILABLE',
                    Product::STATUS_AVAILABLE => 'AVAILABLE'
                ],
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.product', 'Select status') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
            ])->label(Yii::t('XcoinModule.product', 'Status'));
            ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'name')->textInput()
                ->hint(Yii::t('XcoinModule.product', 'Please enter your product name')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textInput()
                ->hint(Yii::t('XcoinModule.product', 'Please enter a short description for your product')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'content')->widget(RichTextField::class, ['preset' => 'full'])
                ->hint(Yii::t('XcoinModule.product', 'Please enter a detailed description for your product')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'country')->widget(Select2::class, [
                'data' => iso3166Codes::$countries,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.product', 'Select country') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->hint(Yii::t('XcoinModule.product', 'Please enter your country')) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'city')->textInput()
                ->hint(Yii::t('XcoinModule.product', 'Please enter your city')) ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'offer_type')->widget(Select2::class, [
                'data' => Product::getOfferTypes(),
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.product', 'Select offer type') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                ],
                'pluginEvents' => [
                    "select2:select" => new JsExpression("function() {  
                     var offer_type = $(this).val();
                     if(offer_type == 1){
                        $('#product-price').hide();
                        $('#product-payment-type').hide();
                        $('#product-discount').show();
                     } else {
                        $('#product-price').show();
                        $('#product-payment-type').show();
                        $('#product-discount').hide();
                     }
                }"),]
            ])->hint(Yii::t('XcoinModule.product', 'Please choose the type of you offer')) ?>

        </div>
        <div class="col-md-6" id="product-price" style="display: <?= $model->hasErrors('price') || $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS ? "block" :  "none"; ?>">
            <?= $form->field($model, 'price')->input('number', ['min' => 1])
                ->hint(Yii::t('XcoinModule.product', 'Please enter a price for your product')) ?>
        </div>
        <div class="col-md-12" id="product-discount" style="display: <?= $model->hasErrors('discount') || $model->offer_type == Product::OFFER_DISCOUNT_FOR_COINS ? "block" :  "none"; ?>">
            <?= $form->field($model, 'discount')->input('number', ['min' => 0.01, 'max' => 100])
                ->hint(Yii::t('XcoinModule.product', 'Please enter the discount in percentage')) ?>
        </div>
        <div class="col-md-12" id="product-payment-type" style="display: <?= $model->hasErrors('payment_type') || $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS ? "block" :  "none"; ?>">
            <?=
            $form->field($model, 'payment_type')->widget(Select2::class, [
                'data' => Product::getPaymentTypes(),
                'options' => ['placeholder' => '- Select payment type - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                ]
            ])->hint(Yii::t('XcoinModule.product', 'Please choose the payment type for your product')); ?>
        </div>
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-2">
                    <?= $upload->button([
                        'label' => true,
                        'tooltip' => false,
                        'options' => ['accept' => 'image/*'],
                        'cssButtonClass' => 'btn-default btn-sm',
                        'dropZone' => '#product-form',
                        'max' => 7,
                    ]) ?>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-9">
                    <?= $upload->preview([
                        'options' => ['style' => 'margin-top:10px'],
                        'model' => $model,
                        'showInStream' => true,
                    ]) ?>
                </div>
            </div>
            <br>
            <?= $upload->progress() ?>
            <p class="help-block">
                <?= Yii::t('XcoinModule.product', 'Please note that first picture will be used as cover for your product.') ?>
            </p>
        </div>
    </div>
</div>
<hr>
<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.product', 'Next')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

