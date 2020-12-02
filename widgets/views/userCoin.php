<?php

use humhub\modules\xcoin\assets\Assets;

Assets::register($this);

/* @var $assets array */
/* @var $spaces array */
/* @var $cssClass string */

?>

<div class="userCoins <?= $cssClass ?>">
    <div class="coinsHeader">
        <h2>
            <span>Coins</span>
            <i class="fa fa-question-circle"></i>
        </h2>
        <a href="<?= $user->createUrl('/xcoin/overview') ?>" class="accountDetail">Account Details</a>
    </div>
    <div class="coinsBody">
        <?php if($coins==null) :?>
            <span>No Coins found.<span>
        <?php endif;?>
        <?php foreach($coins as $coin) : ?>
           <div class="coin">
               <a class="tt myCoinBlock" title=""
                  data-toggle="tooltip" data-placement="top"
                  data-original-title="<?= $coin['name'] ?>"
                  >
               <div class="myCoinName" style="background-color:<?= $coin['color'] ?>;">
                   <?php
                   $pos=strpos($coin['name'], ' ');
                   if($pos!=false){
                       echo substr($coin['name'],0,1).substr($coin['name'],$pos+1,1);
                   }
                   
                   ?>
               </div>
               </a>
               <span class="amountCoin">
                   <?= round($coin['balance'],1)?></span>
           </div>
        <?php endforeach;?>
    </div>
</div>
