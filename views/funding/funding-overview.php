<?php
/**
 * Created By IJ
 * @author : Ala Daly <ala.daly@dotit-corp.com>
 * @date : 12‏/10‏/2021, Tue
 **/

use humhub\assets\Select2BootstrapAsset;
use humhub\modules\xcoin\models\Funding;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use yii\helpers\Html;
use yii\helpers\Url;

Select2BootstrapAsset::register($this);

/**
 * @var $model Funding
 */
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.funding', '<strong>Congratulations</strong><h3><strong>Your Project </strong></h3><h3>is ready for review </h3>'), 'closable' => true]) ?>
<?php $form = ActiveForm::begin(['id' => 'account-form']); ?>
<?= Html::hiddenInput('overview', '1'); ?>
<?= $form->field($model, 'challenge_id')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'amount', ['enableError' => false])->hiddenInput()->label(false)->hint(false) ?>
<?= $form->field($model, 'exchange_rate', ['enableError' => false])->hiddenInput()->label(false)->hint(false) ?>
<?= $form->field($model, 'title')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'description')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'content')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'country')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'city')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'deadline')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'youtube_link')->hiddenInput()->label(false) ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>
<div class="modal-body">
    <div class="row text-center">
        <div class="col-md-12">
            <?= Yii::t('XcoinModule.transaction', 'After review and approval by marketplace admin , your project will be visible in this public space') ?>
            <a class="add-challenge"
               href="<?= Url::to($model->space->getUrl()) ?>"
               data-toggle="tooltip" style="color: #3cbeef; margin-top: 4px">
                <span class="text"><?= Yii::t('XcoinModule.challenge', 'link') ?></span>
            </a>
        </div>
    </div>
    <div class="row text-center" style="margin-top: 20px">
        <div class="col-md-12">
            <?= Yii::t('XcoinModule.transaction',
                'You and other members can see your project on your page following the link below.From there , you can edit your project anytime')
            ?>

        </div>
    </div>

</div>
<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.funding', 'See created project')); ?>
</div>
<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
