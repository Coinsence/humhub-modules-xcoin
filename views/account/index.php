<?php

use yii\bootstrap\Html;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\grids\TransactionsGridView;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;

/**
 * @var Account $account
 */
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="pull-right">
            <?= Html::a(Yii::t('XcoinModule.base', 'Back to overview'), ['/xcoin/overview', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default']); ?>
            <?php if (AccountHelper::canManageAccount($account)) : ?>
                <?php if ($account->account_type == Account::TYPE_STANDARD): ?>
                    <?= Html::a(Yii::t('XcoinModule.base', 'Edit'), ['/xcoin/account/edit', 'id' => $account->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-default', 'data-target' => '#globalModal']); ?>
                <?php endif; ?>
                <?= Html::a(Yii::t('XcoinModule.base', 'Transfer'), ['/xcoin/transaction/transfer', 'accountId' => $account->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-success', 'data-target' => '#globalModal']); ?>
            <?php endif; ?>
        </div>
        <?= Yii::t('XcoinModule.base', '<strong>Account overview:</strong> ' . $account->title); ?>
    </div>

    <div class="panel-body">

        <br />
        <br />
        <table class="table">
            <tr>
                <td colspan="2"><strong><?= Yii::t('XcoinModule.base', 'Account summary'); ?></strong></td>
            </tr>
            <?php if ($account->ethereum_address) :?>
            <tr>
                <td><strong>Ethereum Address</strong></td>
                <td style="vertical-align: middle;"">
                    <?= Html::a("$account->ethereum_address", "https://rinkeby.etherscan.io/address/$account->ethereum_address", ['target' => '_blank'] )?>
                </td>
            </tr>
            <?php endif; ?>
            <?php if ($account->space): ?>
                <tr>
                    <td><strong><?= Yii::t('XcoinModule.base', 'Owner'); ?></strong></td>
                    <td>
                        <?= SpaceImage::widget(['space' => $account->space, 'width' => 26]); ?>
                        <?= Html::encode($account->space->name); ?>
                    </td>
                </tr>
                <tr>
                    <td><strong><?= Yii::t('XcoinModule.base', 'Manager'); ?></strong></td>
                    <td>
                        <?php if ($account->space && !$account->user): ?>
                            <?= Yii::t('XcoinModule.base', 'Account managed by the actual Space owner'); ?>
                        <?php elseif ($account->user): ?>
                            <?= UserImage::widget(['user' => $account->user, 'width' => 26]); ?>
                            <?= Html::encode($account->user->displayName); ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td width="100"><strong><?= Yii::t('XcoinModule.base', 'Balance'); ?></strong></td>
                <td>
                    <?php
                    $list = [];
                    foreach ($account->getAssets() as $asset) {
                        $list[] = '<strong>' . $account->getAssetBalance($asset) . '</strong>&nbsp; ' .
                                SpaceImage::widget(['space' => $asset->space, 'width' => 20, 'showTooltip' => true, 'link' => true]) . '</span>';
                    }
                    if (empty($list)) {
                        echo Yii::t('XcoinModule.base', 'No assets available');
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
        <br />


        <?= TransactionsGridView::widget(['account' => $account, 'contentContainer' => $this->context->contentContainer]) ?>
    </div>
</div>
