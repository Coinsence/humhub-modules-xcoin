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
use PhpOffice\PhpSpreadsheet\Helper\Html;
use yii\web\JsExpression;

/** @var $model Experience */

Assets::register($this);
?>


<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.profile', 'Provide Profile details'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>

<div class="modal-body">
    <div class="row">
        
        <div class="col-md-12">
        <?php foreach($model as $mod) :?>
    
          <?= $form->field($mod, 'profile_offer')->textarea(['rows' => 3])
                ->hint(Yii::t('XcoinModule.profile', 'Please enter What I offer to the community')) ?>
       <?php endforeach;?>
        </div>
       
       
        
     
    </div>
</div>
<hr>
<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.profile', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>