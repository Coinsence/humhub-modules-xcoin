<?php

use humhub\modules\xcoin\models\Product;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use yii\helpers\Html;
use yii\web\JsExpression;
use kartik\widgets\Select2;

/** @var $products array */
/** @var $model Product */

Select2BootstrapAsset::register($this);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.product', 'Copy existing data?'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'product-form']); ?>
<?= Html::hiddenInput('step', '1'); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?=
            $form->field($model, 'clone_id')->widget(Select2::class, [
                'data' => $products,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.product', 'Select existing product') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->label(Yii::t('XcoinModule.product', 'Use Existing Product Data ?'))
            ->hint(Yii::t('XcoinModule.product', 'Leave empty to create a new Product Dataset'));
            ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.product', 'Next')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
