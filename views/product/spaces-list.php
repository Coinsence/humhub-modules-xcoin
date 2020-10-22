<?php

use humhub\modules\space\models\Space;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Product;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use kartik\widgets\Select2;
use yii\bootstrap\Html;
use yii\web\JsExpression;

/** @var $product Product */
/** @var $spacesList Space[] */
/** @var $defaultSpace Space */

Assets::register($this);
?>


<?php ModalDialog::begin(['header'=> Yii::t('XcoinModule.product', 'Select space') , 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'product-form']); ?>
<?= Html::hiddenInput('step', '-1'); ?>

<div class="modal-body">
    <div class="row" style=" display: flex; align-items: center;">
        <div class="col-md-10">
            <?=
            $form->field($product, 'space_id')->widget(Select2::class, [
                'data' => $spacesList,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.product', 'Select space') . ' - '],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])
                ->label(Yii::t('XcoinModule.product', 'Select from which space you want to create your product'))
                ->hint(Yii::t('XcoinModule.product', 'Leave empty if you want to create automatically a new space for this product'));
            ?>
        </div>
        <div class="col-md-2">
            <?= ModalButton::submitModal(null, Yii::t('XcoinModule.product', 'Next')); ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>

<style>
    .modal-footer button {
        width: 100%;
    }
</style>
