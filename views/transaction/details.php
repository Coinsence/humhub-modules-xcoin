<?php

use humhub\modules\algorand\utils\Helpers;
use humhub\modules\xcoin\models\Account;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use yii\helpers\StringHelper;

/** @var $transaction */
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.transaction', '<strong>Transfer</strong> details'), 'closable' => true]) ?>
<div class="modal-body">

    <table class="table table-condensed">

        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Algorand TxID') ?></td>
            <td>
                <?= Html::a(
                    StringHelper::truncate($transaction->id, 30, '...'),
                    " https://algoexplorer.io/tx/$transaction->id",
                    [
                        'target' => '_blank',
                        'title' => $transaction->id,
                        'data-toggle' => 'tooltip',
                    ]) ?>
            </td>
        </tr>
        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Date') ?></td>
            <td><?= Yii::$app->formatter->asDateTime($transaction->{'round-time'}, 'short'); ?></td>
        </tr>
        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Amount') ?></td>
            <td><?= Helpers::formatCoinAmount($transaction->{'asset-transfer-transaction'}->amount, true); ?></td>
        </tr>
        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Sender account') ?></td>
            <td><?= Html::encode(Account::findOne(['algorand_address' => $transaction->sender])->id); ?></td>
        </tr>
        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Target account') ?></td>
            <td><?= Html::encode(Account::findOne(['algorand_address' => $transaction->{'asset-transfer-transaction'}->receiver])->id); ?></td>
        </tr>
    </table>

</div>

<div class="modal-footer">
    <?= ModalButton::cancel('Close'); ?>
</div>
<?php ModalDialog::end() ?>
