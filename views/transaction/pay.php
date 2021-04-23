<?php

use humhub\assets\Select2BootstrapAsset;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\models\Transaction;
use humhub\modules\xcoin\widgets\AmountField;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\helpers\Html;

Select2BootstrapAsset::register($this);

/** @var Transaction $transaction */
/** @var Product $product */
?>

<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.transaction', 'Pay for product'), 'closable' => false]) ?>
<?php $form = ActiveForm::begin(); ?>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label"><?= Yii::t('XcoinModule.transaction', 'Sender account') ?></label>
                <div class="form-control" style="padding-top:4px;">
                    <?= UserImage::widget(['user' => $transaction->fromAccount->user, 'width' => 24]); ?>
                    <?= $transaction->fromAccount->title; ?>
                    <?= Html::a(
                        'Change',
                        [
                            '/xcoin/transaction/select-account',
                            'container' => $this->context->contentContainer,
                            'productId' => $product->id
                        ],
                        [
                            'class' => 'btn btn-sm btn-default pull-right',
                            'style' => 'margin-top:-2px;margin-right:-10px',
                            'data-target' => '#globalModal',
                            'data-ui-loader' => ''
                        ]
                    ); ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <?= $form->field($transaction, 'amount')
                ->widget(AmountField::class, ['asset' => $product->marketplace->asset, 'readonly' => true])
                ->label(Yii::t('XcoinModule.transaction', 'Requested amount')
            ); ?>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label"><?= Yii::t('XcoinModule.transaction', 'Recipient account') ?></label>
                <div class="form-control" style="padding-top:4px;">
                    <?php if($transaction->toAccount->space !== null) : ?>
                        <?= SpaceImage::widget(['space' => $transaction->toAccount->space, 'width' => 24]); ?>
                    <?php else : ?>
                        <?= UserImage::widget(['user' => $transaction->toAccount->user, 'width' => 24]); ?>
                    <?php endif; ?>
                    <?= $transaction->toAccount->title; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer">
    <?= ModalButton::submitModal(); ?>
    <?= ModalButton::cancel(); ?>
</div>

<?php ActiveForm::end(); ?>
<?php ModalDialog::end() ?>
