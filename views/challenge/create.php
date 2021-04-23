<?php

use humhub\modules\content\widgets\richtext\RichTextField;
use humhub\modules\file\widgets\Upload;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\widgets\AmountField;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\widgets\ActiveForm;
use humhub\assets\Select2BootstrapAsset;
use yii\web\JsExpression;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;

/** @var $model Challenge */
/** @var $assets Asset[] */
/** @var $defaultAsset Asset */

Select2BootstrapAsset::register($this);

$upload = Upload::forModel($model, $model->coverFile);
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.challenge', 'Create Challenge'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'challenge-form']); ?>
<?= $form->field($model, 'space_id')->hiddenInput()->label(false) ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'title')->textInput()->hint(Yii::t('XcoinModule.challenge', 'Please enter your challenge title')) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'description')->widget(RichTextField::class, ['preset' => 'full'])
                ->hint(Yii::t('XcoinModule.challenge', 'Please enter your challenge description')) ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'asset_id')->widget(Select2::class, [
                'data' => $assets,
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.challenge', 'Select coin') . ' - ', 'value' => ($defaultAsset) ? $defaultAsset->id : ''],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->label(Yii::t('XcoinModule.funding', 'Requested coin'));
            ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'selectedReward')->widget(SwitchInput::class, [
                'name' => 'selected_reward',
                'type' => SwitchInput::RADIO,
                'items' => [
                    ['label' => 'Any project Issued Coin', 'value' => 'any_project_coin', 'id' => 'any_project_coin'],
                    ['label' => 'No rewarding', 'value' => 'no_rewarding'],
                    ['label' => 'Project must Offer', 'value' => 'specific_project_coin'],
                ],
                'inlineLabel' => 'true',
                'pluginEvents' => [
                    "switchChange.bootstrapSwitch" => new JsExpression("function() {  
                     var offer_type = $(this).val();
                     if(offer_type == 'specific_project_coin'){
                        $('#challenge-exchange_rate,input#challenge-exchange_rate').show();
                        $('#challenge-specific_project_coin').show();
                     } else {
                        $('#challenge-exchange_rate,#challenge-exchange_rate').hide();
                        $('#select2-challenge-specific_project_coin-container').hide();
                     }
                }"),]
            ]);
            ?>
            <?= $form->field($model, 'exchange_rate')
                ->widget(AmountField::class)
                ->label(Yii::t('XcoinModule.challenge', 'Project must offer')
                );
            ?>
            <?=
            $form->field($model, 'specific_project_coin')->widget(Select2::class, [
                'data' => $assets,
                'id'=>'test',
                'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.challenge', 'Select coin') . ' - ', 'value' => ($defaultAsset) ? $defaultAsset->id : '','class'=>'hide'],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => true,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ])->label(Yii::t('XcoinModule.funding', 'Requested coin'));
            ?>
        </div>
        <div class="col-md-12">
            <label class="control-label"><?= Yii::t('XcoinModule.challenge', 'Challenge Image') ?></label><br>
            <div class="col-md-2">
                <?= $upload->button([
                    'label' => true,
                    'tooltip' => false,
                    'options' => ['accept' => 'image/*'],
                    'cssButtonClass' => 'btn-default btn-sm',
                    'dropZone' => '#challenge-form',
                    'max' => 1,
                ]) ?>

            </div>
            <div class="col-md-1"></div>
            <div class="col-md-9">
                <?= $upload->preview([
                    'options' => ['style' => 'margin-top:10px'],
                    'model' => $model,
                    'showInStream' => true,
                ]) ?>
            </div>
            <br>
            <?= $upload->progress() ?>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.challenge', 'Save')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
