<?php

use humhub\libs\Iso3166Codes;
use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Category;
use humhub\modules\xcoin\models\Product;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;

/** @var $model Product */
/** @var $myAsset Asset */

?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.product', 'Provide details'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'product-form']); ?>

<?= Html::hiddenInput('step', '2'); ?>

<?= $form->field($model, 'marketplace_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'product_type')->hiddenInput()->label(false) ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'name')->textInput() ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textInput() ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'content')->widget(RichTextField::class, ['preset' => 'full']) ?>
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
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'city')->textInput() ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'categories_names')->widget(Select2::class, [
                'model' => $model,
                'attribute' => 'categories_names',
                'data' => ArrayHelper::map(
                    Category::find()
                        ->alias('category')
                        ->leftJoin('xcoin_marketplace_category mc','mc.category_id = category.id')
                        ->where('mc.marketplace_id = '. $model->marketplace_id)
                        ->all()
                    , 'name', 'name'),
                'options' => [
                    'multiple' => true,
                ]
            ])->label('Categories'); ?>
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
                        $('#first-payemnt').hide();
                        $('#call-type').hide();
                        $('#product-discount').show();
                     } else {
                        $('#product-price').show();
                        $('#product-payment-type').show();
                        $('#first-payemnt').show();
                        $('#call-type').show();
                        $('#product-discount').hide();
                     }
                }"),]
            ]) ?>

        </div>
        <div class="col-md-6" id="product-price"
             style="display: <?= $model->hasErrors('price') || $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS ? "block" : "none"; ?>">
            <?= $form->field($model, 'price')->input('number', ['min' => 1]) ?>
        </div>
        <div class="col-md-12" id="product-discount"
             style="display: <?= $model->hasErrors('discount') || $model->offer_type == Product::OFFER_DISCOUNT_FOR_COINS ? "block" : "none"; ?>">
            <?= $form->field($model, 'discount')->input('number', ['min' => 0.01, 'max' => 100]) ?>
        </div>
        <div class="col-md-12" id="product-payment-type"
             style="display: <?= $model->hasErrors('payment_type') || $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS ? "block" : "none"; ?>">
            <?=
            $form->field($model, 'payment_type')->widget(Select2::class, [
                'data' => Product::getPaymentTypes(),
                'options' => ['placeholder' => '- Select unit - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                ]
            ])->hint(Yii::t('XcoinModule.product', 'Please choose the Offer unit for your product')); ?>
        </div>
        <div class="col-md-6" id="first-payemnt"
             style="display: <?= $model->hasErrors('request_paytment_first') || $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS ? "block" : "none"; ?>">
             <input type="hidden" value="0" name="Model[request_paytment_first]">
            <?= $form->field($model, 'request_paytment_first')->checkBox(['label' => 'Request Payment first','data-size'=>'small', 'class'=>'bs_switch'

            ,'style'=>'margin-bottom:4px;', 'id'=>'request_paytment_first']) ?>
        </div>

        <div class="col-md-12" id="call-type"
             style="display: <?= $model->hasErrors('type_call') || $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS ? "block" : "none"; ?>">
            <?=
            $form->field($model, 'type_call')->widget(Select2::class, [
                'data' => Product::getCallTypes(),
                'options' => ['placeholder' => '- Select call type after payement - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                ],
                'pluginEvents' => [
                    "select2:select" => new JsExpression("function() {  
                     var type_call = $(this).val();
                     if(type_call == 1){
                        $('#call-link').hide();
                        $('#call-message').show();
                     } else {
                        $('#call-message').hide();
                        $('#call-link').show();
                        
                     }
                }"),]
            ])->hint(Yii::t('XcoinModule.product', 'Please choose the call type after payement ')); ?>
        </div>
        <div class="col-md-12" id="call-message"
             style="display: <?= $model->hasErrors('message') || $model->type_call == Product::TYPE_MESSAGE ? "block" : "none"; ?>">
            <?= $form->field($model, 'message')->widget(RichTextField::class, ['preset' => 'full'])
                ->hint(Yii::t('XcoinModule.product', 'Please enter a detailed message')) ?>
        </div>
        <div class="col-md-12" id="call-link" 
            style="display: <?= $model->hasErrors('link') || $model->type_call == Product::TYPE_LINK ? "block" : "none"; ?>">
            <?= $form->field($model, 'link')->textInput()
                ->hint(Yii::t('XcoinModule.product', 'Please enter your product call to action link')) ?>
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

<?php 
/*
<?php if ($model->marketplace->isLinkRequired()) : ?>

    <?php endif; ?>*/
?>