<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

/** @var Space $space */

use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AssetHelper;
use yii\bootstrap\Html; ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <strong><?= Yii::t('XcoinModule.ethereum', 'Ethereum') ?></strong>
        <div class="pull-right">
            <?php if (AssetHelper::canManageAssets($this->context->contentContainer)): ?>
                <?php if ($space->eth_status == Space::ETHEREUM_STATUS_DISABLED) : ?>
                    <?= Html::button(Yii::t('XcoinModule.ethereum', 'Enable ethereum'), [
                        'id' => 'ether-enable-btn',
                        'class' => 'btn btn-success btn-sm',
                        'data-target-url' => "{$space->getUrl()}xcoin/ethereum/enable"
                    ]);
                    ?>
                <?php endif; ?>
                <?php if ($space->eth_status == Space::ETHEREUM_STATUS_ENABLED) : ?>
                    <?= Html::button(Yii::t('XcoinModule.ethereum', 'Migrate missing transactions'), [
                        'id' => 'ether-enable-btn',
                        'class' => 'btn btn-success btn-sm',
                        'data-target-url' => "{$space->getUrl()}xcoin/ethereum/migrate-transactions"
                    ]);
                    ?>
                    <?= Html::button(Yii::t('XcoinModule.ethereum', 'Synchronize Balances'), [
                        'id' => 'ether-enable-btn',
                        'class' => 'btn btn-success btn-sm',
                        'data-target-url' => "{$space->getUrl()}xcoin/ethereum/synchronize-balances"
                    ]);
                    ?>
                <?php endif; ?>
            <?php endif; ?>
            <?= Html::a(Yii::t('XcoinModule.overview', 'Back to overview'), ['/xcoin/overview', 'container' => $this->context->contentContainer], ['class' => 'btn btn-default btn-sm']); ?>
        </div>
    </div>
    <div class="panel-body">
        <div class="alert alert-info"
             id="ethereum-loader" <?php if ($space->dao_address && $space->coin_address || $space->eth_status != Space::ETHEREUM_STATUS_IN_PROGRESS) : ?> style="display: none" <?php endif; ?>>
            <?= Yii::t('XcoinModule.ethereum', 'Space Ethereum migration is in progress ! This could take some minutes.') ?>
            <div class="loader humhub-ui-loader pull-right" style="padding: 0">
                <div class="sk-spinner sk-spinner-three-bounce">
                    <div class="sk-bounce1"></div>
                    <div class="sk-bounce2"></div>
                    <div class="sk-bounce3"></div>
                </div>
            </div>
        </div>
        <table class="table">
            <tr>
                <td colspan="2"><strong><?= Yii::t('XcoinModule.ethereum', 'Ethereum summary') ?></strong></td>
            </tr>
            <tr>
                <td><strong><?= Yii::t('XcoinModule.ethereum', 'Dao Address') ?></strong></td>
                <td style="vertical-align: middle;">
                    <?php if ($space->dao_address) : ?>
                        <?= Html::a($space->dao_address, "https://algoexplorer.io/address/$space->dao_address", ['target' => '_blank']) ?>
                    <?php else : ?>
                        <span class="label label-default"><?= Yii::t('XcoinModule.ethereum', 'unavailable') ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><strong><?= Yii::t('XcoinModule.ethereum', 'Coin Address') ?></strong></td>
                <td style="vertical-align: middle;">
                    <?php if ($space->coin_address) : ?>
                        <?= Html::a($space->coin_address, "https://algoexplorer.io/address/$space->coin_address", ['target' => '_blank']) ?>
                        <br>
                        <?= Html::a(Html::img("https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=ethereum:{$space->coin_address}&choe=UTF-8", ['alt' => 'ethereum address', 'style' => 'width: 100%; max-width: 250px']), "https://algoexplorer.io/address/$space->coin_address", ['target' => '_blank']) ?>
                    <?php else : ?>
                        <span class="label label-default"><?= Yii::t('XcoinModule.ethereum', 'unavailable') ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
</div>
