<?php

use humhub\modules\xcoin\assets\Assets;

Assets::register($this);

/* @var $assets array */
/* @var $spaces array */
/* @var $cssClass string */

?>

<div class="userCoins <?= $cssClass ?>">
    <div class="coinsHeader">
        <h2><span>Coins</span><i class="fa fa-question-circle"></i></h2>
        <a href="<?= $user->createUrl('/xcoin/overview') ?>" class="accountDetail">Account Details</a>
    </div>
    <div class="coinsBody">
        <?php foreach($assets as $asset) : ?>
           <div class="coin">
               <a class="tt" title=""
                  data-toggle="tooltip" data-placement="top"
                  data-original-title="<?= $asset['name'] ?>"
                  style="border-radius: 50%; margin-right: 2px; background-color:<?= $asset['color'] ?>;">
                   <?= substr($asset['name'], 0, 1) ?>
               </a>
               <span class="amountCoin"><?=$asset['sum(xcoin_transaction.amount)'] ?></span>
           </div>
        <?php endforeach;?>
    </div>
</div>
