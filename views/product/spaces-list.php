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


<?php ModalDialog::begin(['header'=> Yii::t('XcoinModule.product', 'Create product') , 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'product-form']); ?>
<?= Html::hiddenInput('step', '-1'); ?>
<?= Html::hiddenInput('personal-product', '0', ['id' => 'personal-product']); ?>

<div class="modal-body">
    <div class="row" style="text-align: center;">
        <?= ModalButton::submitModal(null, Yii::t('XcoinModule.product', 'Create your personal product'))->id('submit-personal-product'); ?>
    </div>
    <!-- TODO: move styles in the div below in scss files -->.
    <div style="width: 100%; height: 17px; border-bottom: 1px solid #aeaeae; text-align: center; margin: 20px 0 30px 0;">
        <span style="font-size: 22px; background-color: #F3F5F6; padding: 0 10px;">
            <?= Yii::t('XcoinModule.product', 'OR') ?>
        </span>
    </div>
    <div class="row" style="display: flex; align-items: center;">
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
        <hr>
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
