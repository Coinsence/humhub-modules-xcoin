<?php

use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;

/** @var $coin string */
/** @var $model yii\base\DynamicModel */

?>

<?php ModalDialog::begin(['header' => 'Buy ' . $coin, 'closable' => true]) ?>
<?php $form = ActiveForm::begin(['id' => 'purchase-form']); ?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'amount')->textInput(['type' => 'number', 'min' => 0])->label(Yii::t('XcoinModule.funding', 'Amount')); ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'Checkout')); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
