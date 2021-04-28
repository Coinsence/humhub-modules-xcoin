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
use kartik\switchinput\SwitchInputAsset;
use yii\web\JsExpression;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;

/** @var $model Challenge */
/** @var $assets Asset[] */
/** @var $defaultAsset Asset */

Select2BootstrapAsset::register($this);

$upload = Upload::forModel($model, $model->coverFile);
SwitchInputAsset::register($this);
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
        <div class="col-md-12">
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
        <div class="col-md-12">
            <?= $form->field($model, 'any_reward_asset')->radio(['onclick' => 'document.getElementById("challenge-no_rewarding").checked = false;
                document.getElementById("challenge-specific_reward_asset").checked = false; 
                document.getElementById("challenge-label_exchange_rate").style.visibility = "hidden"; 
                document.getElementById("challenge-label_specific_reward_asset").style.visibility = "hidden"; 
                document.getElementById("challenge-exchange_rate").style.visibility = "hidden"; 
                document.getElementById("challenge-specific_reward_asset_id").parentElement.style.visibility = "hidden";
                ',
            ]);
            ?>
            <?= $form->field($model, 'no_rewarding')->radio(['onclick' => 'document.getElementById("challenge-any_reward_asset").checked = false;
                document.getElementById("challenge-specific_reward_asset").checked = false; 
                document.getElementById("challenge-label_exchange_rate").style.visibility = "hidden"; 
                document.getElementById("challenge-label_specific_reward_asset").style.visibility = "hidden"; 
                document.getElementById("challenge-exchange_rate").style.visibility = "hidden"; 
                document.getElementById("challenge-specific_reward_asset_id").parentElement.style.visibility = "hidden";

                '
            ]);
            ?>
            <?= $form->field($model, 'specific_reward_asset')->radio(['onclick' => 'document.getElementById("challenge-any_reward_asset").checked = false;
                document.getElementById("challenge-no_rewarding").checked = false; 
                document.getElementById("challenge-label_exchange_rate").style.visibility = "visible"; 
                document.getElementById("challenge-exchange_rate").style.visibility = "visible"; 
                document.getElementById("challenge-specific_reward_asset_id").parentElement.style.visibility = "visible";
                document.getElementById("challenge-label_specific_reward_asset").style.visibility = "visible"; 

                 '
            ]);
            ?>
            <div class="col-md-12">
                <div class="col-md-4">
                    <?= $form->field($model, 'exchange_rate')->textInput(['maxlength' => true, 'style' => 'visibility:hidden'
                        , 'placeholder' => 'Set Number'])->label(false) ?>
                </div>
                <div class="col-md-2">
                    <label class="control-label " for="challenge-exchange_rate" style="visibility:hidden "
                           id="challenge-label_exchange_rate">of</label>
                </div>
                <div class="col-md-4">
                    <?=
                    $form->field($model, 'specific_reward_asset_id')->widget(Select2::class, [
                        'data' => $assets,
                        'options' => [
                            'placeholder' => '- ' . Yii::t('XcoinModule.challenge', 'Select coin') . ' - ', 'value' => ($defaultAsset) ? $defaultAsset->id : '',
                            'class' => 'hide',
                        ],
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => false,
                            'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                        ],
                    ])->label(false);
                    ?>
                </div>
                <div class="col-md-2">
                    <label class="control-label " style="visibility:hidden"
                           id="challenge-label_specific_reward_asset">for each invested COIN </label>
                </div>
            </div>
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
<script>
    $(document).ready(function () {
        document.getElementById("challenge-specific_reward_asset_id").parentElement.style.visibility = "hidden";
    });
</script>