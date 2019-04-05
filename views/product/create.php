<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use kartik\widgets\Select2;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use yii\web\JsExpression;

/**
 * @var $assetList array
 */

Select2BootstrapAsset::register($this);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.base', 'Sell Product'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<div class="modal-body">
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
        $form->field($model, 'asset_id')->widget(Select2::classname(), [
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
    <?php /*
    <div class="col-md-12">
        <?= $form->field($model, 'pictureFile')->fileInput() ?>
    </div>
    */ ?>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('base', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

