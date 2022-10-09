<?php

use humhub\modules\xcoin\grids\AccountVouchersGridView;
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
            <?= Html::a(Yii::t('XcoinModule.account', 'Create'), ['/xcoin/account/create-voucher', 'accountId' => $account->id, 'container' => $this->context->contentContainer], ['class' => 'btn btn-default', 'data-target' => '#globalModal']); ?>
            <?= Html::a(Yii::t('XcoinModule.account', 'Redeem'), ['/xcoin/account/redeem-voucher', 'accountId' => $account->id,'container' => $this->context->contentContainer], ['class' => 'btn btn-default', 'data-target' => '#globalModal']); ?>

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


        <?= AccountVouchersGridView::widget(['account' => $account, 'contentContainer' => $this->context->contentContainer]) ?>
    </div>
</div>
