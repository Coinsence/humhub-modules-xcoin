<?php

use humhub\modules\xcoin\models\Funding;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use yii\helpers\Html;
use yii\web\JsExpression;
use kartik\widgets\Select2;

/** @var $fundings array */
/** @var $model Funding */

Select2BootstrapAsset::register($this);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.product', 'Copy existing data?'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'product-form']); ?>
<?= Html::hiddenInput('step', '2'); ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?=
            $form->field($model, 'clone_id')->widget(Select2::class, [
                'data' => $fundings,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Select existing project') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->label(Yii::t('XcoinModule.funding', 'Use Existing Project Data ?'))
                ->hint(Yii::t('XcoinModule.funding', 'Leave empty to create a new Project Dataset'));
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
