<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\file\widgets\Upload;
use humhub\modules\xcoin\models\Product;
use kartik\widgets\Select2;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use yii\web\JsExpression;

/** @var $assetList array */
/** @var $model Product */
/** @var $defaultAsset Asset */

Select2BootstrapAsset::register($this);
$upload = Upload::forModel($model, $model->pictureFile);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.product', 'Sell Product'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'product-form']); ?>

<div class="modal-body">
    <div class="row">
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
        <div class="col-md-6">
            <?=
            $form->field($model, 'asset_id')->widget(Select2::class, [
                'data' => $assetList,
                'options' => ['placeholder' => '- Select asset - ', 'value' => ($defaultAsset) ? $defaultAsset->id : []],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ]
            ])->hint(Yii::t('XcoinModule.product', 'Please choose the type of coin you are accepting')) ?>
        </div>
        <div class="col-md-6" id="product-price" style="display: <?= $model->hasErrors('price') || $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS ? "block" :  "none"; ?>">
            <?= $form->field($model, 'price')->input('number', ['min' => 0.01])
                ->hint(Yii::t('XcoinModule.product', 'Please enter a price for your product')) ?>
        </div>
        <div class="col-md-12" id="product-discount" style="display: <?= $model->hasErrors('discount') || $model->offer_type == Product::OFFER_DISCOUNT_FOR_COINS ? "block" :  "none"; ?>">
            <?= $form->field($model, 'discount')->input('number', ['min' => 0.01, 'max' => 100])
                ->hint(Yii::t('XcoinModule.product', 'Please enter the discount in percentage')) ?>
        </div>
        <div class="col-md-6" id="product-payment-type" style="display: <?= $model->hasErrors('payment_type') || $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS ? "block" :  "none"; ?>">
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
            <label class="control-label" for="product-price"><?= Yii::t('XcoinModule.product', 'Picture') ?></label><br>
            <div class="col-md-6">
                <?= $upload->button([
                    'label' => true,
                    'options' => ['accept' => 'image/*'],
                    'dropZone' => '#product-form',
                    'max' => 1,
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $upload->preview() ?>
            </div>
            <?= $upload->progress() ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.product', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

