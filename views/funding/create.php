<?php

use humhub\modules\xcoin\models\Asset;
use yii\bootstrap\Html;
use yii\helpers\Url;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\modules\user\widgets\UserPickerField;
use humhub\assets\Select2BootstrapAsset;
use yii\web\JsExpression;
use kartik\widgets\Select2;

/** @var $assetList array */
/** @var $defaultAsset Asset */

Select2BootstrapAsset::register($this);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.base', 'Select wanted asset'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>
<?= Html::hiddenInput('step', '1'); ?>
<div class="modal-body">
    <?=
    $form->field($model, 'asset_id')->widget(Select2::classname(), [
        'data' => $assetList,
        'options' => ['placeholder' => '- Select asset - ', 'value' => ($defaultAsset) ? $defaultAsset->id : []],
        'theme' => Select2::THEME_BOOTSTRAP,
        'hideSearch' => true,
        'pluginOptions' => [
            'allowClear' => false,
            'escapeMarkup' => new JsExpression("function(m) { return m; }"),
        ],
    ]);
    ?>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('base', 'Next')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
