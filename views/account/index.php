<?php

use yii\bootstrap\Html;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\grids\TransactionsGridView;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;

/**
 * @var Account $account
 * @var $allowDirectCoinTransfer
 */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.account', 'Back to overview'), ['/xcoin/overview', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default']); ?>
            <?php if (AccountHelper::canManageAccount($account)) : ?>
                <?php if ($account->account_type == Account::TYPE_STANDARD): ?>
                    <?= Html::a(Yii::t('XcoinModule.account', 'Edit'), ['/xcoin/account/edit', 'id' => $account->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-default', 'data-target' => '#globalModal']); ?>
                <?php endif; ?>
                <?php if (!isset($allowDirectCoinTransfer) || $allowDirectCoinTransfer): ?>
                    <?= Html::a(Yii::t('XcoinModule.account', 'Transfer'), ['/xcoin/transaction/transfer', 'accountId' => $account->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-default', 'data-target' => '#globalModal']); ?>
                <?php else: ?>
                    <?= Html::a(
                        Yii::t('XcoinModule.account', 'Transfer'),
                        ['/xcoin/transaction/transfer',
                            'accountId' => $account->id,
                            'container' => $this->context->contentContainer],
                        ['class' => 'btn btn-default',
                            'disabled' => 'disabled',
                            'onclick' => 'return false;',
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'right',
                            'title' => Yii::t('XcoinModule.base', 'Direct coin transfer disabled by the space admin')]); ?>
                <?php endif ?>
            <?php endif; ?>
        </div>
        <?= '<strong>' . Yii::t('XcoinModule.account', 'Account overview:') . '</strong> ' . $account->title; ?>
    </div>

    <div class="panel-body">

        <br/>
        <br/>
        <table class="table">
            <tr>
                <td colspan="2"><strong><?= Yii::t('XcoinModule.account', 'Account summary') ?></strong></td>
            </tr>
            <?php if ($account->algorand_address) : ?>
                <tr>
                    <td><strong><?= Yii::t('XcoinModule.account', 'Algorand Address') ?></strong></td>
                    <td style="vertical-align: middle; text-align: center;">
                        <?= Html::a(Html::img("https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=https://testnet.algoexplorer.io/address/{$account->algorand_address}&choe=UTF-8", ['alt' => 'algorand address', 'style' => 'width: 100%; max-width: 180px']), "https://rinkeby.etherscan.io/address/$account->algorand_address", ['target' => '_blank', 'class' => 'eth-qr-code']) ?>
                        <br>
                        <?= Html::a($account->algorand_address, "https://testnet.algoexplorer.io/address/$account->algorand_address", ['target' => '_blank', 'style' => 'font-size: 10px;', 'class' => 'eth-address']) ?>
                    </td>
                </tr>
            <?php endif; ?>
            <?php if ($account->space): ?>
                <tr>
                    <td><strong><?= Yii::t('XcoinModule.account', 'Owner') ?></strong></td>
                    <td>
                        <?= SpaceImage::widget(['space' => $account->space, 'width' => 26]); ?>
                        <?= Html::encode($account->space->name); ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?= Yii::t('XcoinModule.account', 'Manager') ?></strong></td>
                    <td>
                        <?php if ($account->space && !$account->user): ?>
                            <?= Yii::t('XcoinModule.account', 'Account managed by the actual Space owner') ?>
                        <?php elseif ($account->user): ?>
                            <?= UserImage::widget(['user' => $account->user, 'width' => 26]); ?>
                            <?= Html::encode($account->user->displayName); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td width="100"><strong><?= Yii::t('XcoinModule.account', 'Balance') ?></strong></td>
                <td>
                    <?php
                    $list = [];
                    foreach ($account->getAssets() as $asset) {
                        $list[] = '<strong>' . $account->getAssetBalance($asset) . '</strong>&nbsp; ' .
                            SpaceImage::widget(['space' => $asset->space, 'width' => 20, 'showTooltip' => true, 'link' => true]) . '</span>';
                    }
                    if (empty($list)) {
                        echo Yii::t('XcoinModule.account', 'No assets available');
                    } else {
                        echo implode('&nbsp;&nbsp;&middot;&nbsp;&nbsp;', $list);
                    }
                    ?>
                </td>
            </tr>
        </table>

        <!--
        <span style="color:red">TODO: Show asset summary</span><br />
        <span style="color:red">TODO: Show Manager / Owner</span><br />
        -->
        <br/>


        <?= TransactionsGridView::widget(['account' => $account, 'contentContainer' => $this->context->contentContainer]) ?>
    </div>
</div>
