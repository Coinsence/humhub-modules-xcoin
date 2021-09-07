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
/** @var $accountsList array */

?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.product', 'Provide details'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'product-form']); ?>

<?= Html::hiddenInput('step', '2'); ?>

<?= $form->field($model, 'marketplace_id')->hiddenInput()->label(false) ?>

    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <?= $form->field($model, 'account')->widget(Select2::class, [
                    'data' => $accountsList,
                    'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.product', 'Select Account') . ' - '],
                    'theme' => Select2::THEME_BOOTSTRAP,
                    'hideSearch' => false,
                    'pluginOptions' => [
                        'allowClear' => false,
                        'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                    ],
                    'value' => 0,
                ])->label(Yii::t('XcoinModule.product', 'Account')) ?>
            </div>
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
                            ->leftJoin('xcoin_marketplace_category mc', 'mc.category_id = category.id')
                            ->where('mc.marketplace_id = ' . $model->marketplace_id)
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
                        $('#payment_first_container').hide();
                        $('#product-discount').show();
                     } else {
                        $('#product-price').show();
                        $('#product-payment-type').show();
                        $('#payment_first_container').show();
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
            <div class="col-md-6" id="payment_first_container"
                 style="display: <?= $model->hasErrors('payment_first') || $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS ? "block" : "none"; ?>">
                <input type="hidden" value="0" name="Model[payment_first]">
                <?= $form->field($model, 'payment_first')
                    ->checkBox([
                        'label' => 'Request Payment first',
                        'data-size' => 'small',
                        'class' => 'bs_switch',
                        'style' => 'margin-bottom:4px;',
                        'id' => 'payment_first'
                    ])
                ?>
            </div>
            <div class="col-md-12">
            <?= $form->field($model, 'is_voucher_product')->checkbox([
                'uncheck' => 0,
                'checked' => 1,
            ]) ?>
            </div>
            <div class="col-md-12" id="product-vouchers" style="display: <?= $model->hasErrors('vouchers') || $model->isVoucherProduct() ? "block" : "none"; ?>">
                <?= $form->field($model, 'vouchers')->textarea(['rows' => 6])
                    ->hint(Yii::t('XcoinModule.product', 'Please enter vouchers list separated by ";"')) ?>
            </div>
            <?php if (!$model->isVoucherProduct()): ?>
                <?php if ($model->marketplace->shouldRedirectToLink()) : ?>
                    <div class="col-md-12" id="cta-link">
                        <?= $form->field($model, 'link')->textInput()
                            ->hint(Yii::t('XcoinModule.product', 'Please enter your product call to action link')) ?>
                    </div>
                <?php else : ?>
                    <div class="col-md-12" id="buy-message">
                        <?= $form->field($model, 'buy_message')->widget(RichTextField::class, ['preset' => 'full'])
                            ->hint(Yii::t('XcoinModule.product', 'Please enter a message to be sent when customer wants to buy your product')) ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
        <?= ModalButton::submitModal(null, Yii::t('XcoinModule.product', 'Next')); ?>
        <?= ModalButton::cancel(); ?>
    </div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
