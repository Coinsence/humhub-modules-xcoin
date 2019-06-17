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
            <?php if (AssetHelper::canManageAssets($this->context->contentContainer) && !$space->dao_address): ?>
                <?= Html::button(Yii::t('XcoinModule.ethereum', 'Enable ethereum'), [
                    'id' => 'ether-enable-btn',
                    'class' => 'btn btn-success btn-sm',
                    'data-target-url' => "{$space->getUrl()}xcoin/ethereum/enable"
                ]);
                ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="panel-body">
        <table class="table">
            <tr>
                <td colspan="2"><strong><?= Yii::t('XcoinModule.ethereum', 'Ethereum summary') ?></strong></td>
            </tr>
            <tr>
                <td><strong><?= Yii::t('XcoinModule.ethereum', 'Dao Address') ?></strong></td>
                <td id="dao-address-container" style="vertical-align: middle;">
                    <?php if ($space->dao_address) : ?>
                        <?= Html::a("$space->dao_address", "https://rinkeby.etherscan.io/address/$space->dao_address", ['target' => '_blank']) ?>
                    <?php else : ?>
                        <span class="label label-default"><?= Yii::t('XcoinModule.ethereum', 'unavailable') ?></span>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><strong><?= Yii::t('XcoinModule.ethereum', 'Coin Address') ?></strong></td>
                <td id="coin-address-container" style="vertical-align: middle;">
                    <?php if ($space->coin_address) : ?>
                        <?= Html::a("$space->coin_address", "https://rinkeby.etherscan.io/token/$space->coin_address", ['target' => '_blank']) ?>
                    <?php else : ?>
                        <span class=" label label-default"><?= Yii::t('XcoinModule.ethereum', 'unavailable') ?></span>
                    <?php endif; ?>
                </td>
            </tr>
        </table>
    </div>
</div>
