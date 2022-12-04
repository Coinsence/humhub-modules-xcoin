<?php

use humhub\assets\Select2BootstrapAsset;
use humhub\libs\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\models\Voucher;
use humhub\modules\xcoin\widgets\AccountField;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use kartik\widgets\Select2;
use yii\web\JsExpression;

/**
 * @var $model Voucher
 */
Select2BootstrapAsset::register($this);
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.transaction', '<strong>Redeem</strong> Voucher'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>
<?= Html::hiddenInput('step', '1'); ?>
<div class="modal-body">
    <hr class="row">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'value'); ?>
        </div>

    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'Submit')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
