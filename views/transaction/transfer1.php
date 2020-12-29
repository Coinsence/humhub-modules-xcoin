<?php

use humhub\assets\Select2BootstrapAsset;
use humhub\libs\Html;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\widgets\AccountField;
use humhub\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

Select2BootstrapAsset::register($this);
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.transaction', '<strong>Transfer</strong> asset'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(['id' => 'asset-form']); ?>
<div class="modal-body">

    <div class="form-group">
        <label class="control-label"><?= Yii::t('XcoinModule.transaction', 'Sender account') ?></label>
        <div  style="padding-top:4px;">
            <?php if ($fromAccount->space !== null): ?>
                <?= SpaceImage::widget(['space' => $fromAccount->space, 'width' => 24]); ?>
            <?php endif; ?>
            <?php if ($fromAccount->user !== null): ?>
                <?= UserImage::widget(['user' => $fromAccount->user, 'width' => 24]); ?>
                <?= $fromAccount->user->username?>
            <?php endif; ?>
            <?= $fromAccount->title; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($transaction, 'amount')
            ->textInput(['readonly' => true, 'value' => $product->price]) ?>
            <b><?= $product->price ?></b>
                        <?= SpaceImage::widget([
                            'space' => $product->marketplace->asset->space,
                            'width' => 24,
                            'showTooltip' => true,
                            'link' => true
                        ]); ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($transaction, 'asset_id')->widget(Select2::class, [
                'data' => $accountAssetList,
                'options' => ['placeholder' =>$accountAssetList,'clearInputs'=>true],
                'theme' => Select2::THEME_BOOTSTRAP,
                'hideSearch' => false,
                'pluginOptions' => [
                    'allowClear' => false,
                    'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                ],
            ]);
            ?>
        </div>
    </div>
    <div>
        <!-- owner image start --><span>Recipient:</span></br>
        <?php if ($product->isSpaceProduct()): ?>
                            <?= SpaceImage::widget([
                                'space' => $product->getSpace()->one(),
                                'width' => 34,
                                'showTooltip' => false,
                                'link' => true
                            ]); ?>
                            <?= " <strong>" . Html::encode($product->getSpace()->one()->name) . "</strong>"; ?>
                        <?php else : ?>
                            <?= UserImage::widget([
                                'user' => $product->getCreatedBy()->one(),
                                'width' => 34,
                                'showTooltip' => false,
                                'link' => true
                            ]); ?>
                            <?= " <strong>" . Html::encode($product->getCreatedBy()->one()->profile->firstname . " " . $product->getCreatedBy()->one()->profile->lastname) . "</strong>"; ?>
                        <?php endif; ?>
                        <!-- owner image end -->
    <hr/>
    
</div>
           
<div class="modal-footer">
    <?= ModalButton::submitModal(null, Yii::t('XcoinModule.product', 'Pay now')); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
