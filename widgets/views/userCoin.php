<?php

use humhub\modules\user\models\User;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\space\widgets\Image as SpaceImage;
use yii\helpers\Url;

Assets::register($this);

/* @var $user User */
/* @var $coins array */
/* @var $cssClass string */

?>

<div class="userCoins <?= $cssClass ?> panel panel-default">
    <div class="panel-heading">
        <strong><?= Yii::t('XcoinModule.account', 'Coins') ?></strong>
        <small><a href="<?= $user->createUrl('/xcoin/overview') ?>" class="accountDetail"><?= Yii::t('XcoinModule.account','Account Details')?></a></small>
    </div>
    <div class="panel-body">
        <?php if(sizeof($coins) === 0) :?>
        <span><?= Yii::t('XcoinModule.account', 'No Coins found')?>.<span>
        <?php else: ?>
            <?php foreach($coins as $coin) : ?>
                <div class="coin">
                    <?= SpaceImage::widget([
                        'space' => $coin['space'],
                        'width' => 24,
                        'showTooltip' => true,
                        'link' => true
                    ]); ?>
                    <span class="amountCoin">
                        <?= round($coin['coin']['balance'],1)?>
                    </span>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </div>
</div>
