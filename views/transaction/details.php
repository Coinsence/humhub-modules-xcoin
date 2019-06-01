<?php

use humhub\modules\xcoin\models\Transaction;
use yii\bootstrap\Html;
use humhub\widgets\ModalButton;
use humhub\widgets\ModalDialog;
use yii\helpers\StringHelper;

/** @var Transaction $transaction */
?>
<?php ModalDialog::begin(['header' => Yii::t('XcoinModule.transaction', '<strong>Transfer</strong> details'), 'closable' => true]) ?>
<div class="modal-body">

    <table class="table table-condensed">
        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Transaction ID') ?></td>
            <td><?= $transaction->id; ?></td>
        </tr>
        <?php if ($transaction->eth_hash) : ?>
            <tr>
                <td><?= Yii::t('XcoinModule.transaction', 'Ethereum transaction Hash') ?></td>
                <td>
                    <?= Html::a(
                        StringHelper::truncate($transaction->eth_hash, 30, '...'),
                        " https://rinkeby.etherscan.io/tx/$transaction->eth_hash",
                        [
                            'target' => '_blank',
                            'title' => $transaction->eth_hash,
                            'data-toggle' => 'tooltip',
                        ]) ?>
                </td>
            </tr>
        <?php endif; ?>
        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Date') ?></td>
            <td><?= $transaction->created_at; ?></td>
        </tr>
        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Amount') ?></td>
            <td><?= $transaction->amount; ?></td>
        </tr>
        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Comment') ?></td>
            <td><?= Html::encode($transaction->comment); ?></td>
        </tr>
        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Sender account') ?></td>
            <td><?= Html::encode($transaction->from_account_id); ?></td>
        </tr>
        <tr>
            <td><?= Yii::t('XcoinModule.transaction', 'Target account') ?></td>
            <td><?= Html::encode($transaction->to_account_id); ?></td>
        </tr>
    </table>

</div>

<div class="modal-footer">
    <?= ModalButton::cancel('Close'); ?>
</div>
<?php ModalDialog::end() ?>
