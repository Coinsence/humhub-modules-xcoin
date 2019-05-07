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
use yii\bootstrap\Html; ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <?= Yii::t('XcoinModule.base', '<strong>Ethereum</strong>'); ?>
    </div>

    <div class="panel-body">
        <table class="table">
            <tr>
                <td colspan="2"><strong><?= Yii::t('XcoinModule.base', 'Ethereum summary'); ?></strong></td>
            </tr>
            <tr>
                <td><strong>Dao Address</strong></td>
                <td style="vertical-align: middle;"">
                <?= Html::a("$space->dao_address", "https://rinkeby.etherscan.io/address/$space->dao_address", ['target' => '_blank'] )?>
                </td>
            </tr>
        </table>
    </div>
</div>
