<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

use humhub\libs\Iso3166Codes;
use humhub\modules\ui\form\widgets\DatePicker;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Experience;
use kartik\widgets\Select2;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use yii\web\JsExpression;

/** @var $model Experience */

Assets::register($this);
?>

<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.experience', 'Provide Profile details'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'experience-form']); ?>

<div class="modal-body">
    <div class="row">
        
        <div class="col-md-12">
            <?= $form->field($model, 'description')->textarea(['rows' => 8])
                ->hint(Yii::t('XcoinModule.experience', 'Please enter What I offer to the community')) ?>
        </div>
       
        
     
    </div>
</div>
<hr>
<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.experience', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>