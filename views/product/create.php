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

Select2BootstrapAsset::register($this);
$upload = Upload::forModel($model, $model->pictureFile);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.base', 'Sell Product'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'product-form']); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'name')->textInput()
                ->hint('Please enter your product name') ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textInput()
                ->hint('Please enter a short description for your product') ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'content')->widget(RichTextField::class, ['preset' => 'full'])
                ->hint('Please enter a detailed description for your product') ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'price')->input('number', ['min' => 0.01])
                ->hint('Please enter a price for your product') ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'asset_id')->widget(Select2::class, [
                'data' => $assetList,
                'options' => ['placeholder' => '- Select asset - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->hint('Please choose the payment asset for your product'); ?>
        </div>
        <div class="col-md-12">
            <?=
            $form->field($model, 'payment_type')->widget(Select2::class, [
                'data' => Product::getPaymentTypes(),
                'options' => ['placeholder' => '- Select payment type - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->hint('Please choose the sale type for your product'); ?>
        </div>
        <div class="col-md-12">
            <label class="control-label" for="product-price">Picture</label><br>
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
    <?= ModalButton::submitModal(null, Yii::t('base', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

