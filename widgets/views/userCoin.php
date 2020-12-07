<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\space\widgets\Image;

Assets::register($this);

/* @var $assets array */
/* @var $spaces array */
/* @var $cssClass string */

?>

<div class="userCoins <?= $cssClass ?>">
    <div class="coinsHeader">
        
            <strong style="font-size: 18px;">Coins</strong>
            
        
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
               
                   
                   <?php
                   $pos=strpos($coin['name'], ' ');
                   if($pos!=false):
                       
                      ?> <img class="current-space-image space-profile-image-<?=$coin['id']?> img-rounded profile-user-photo" src="/uploads/profile_image/<?=$coin['guid']?>.jpg?m=1606922234" alt="<?=substr($coin['name'],0,1).substr($coin['name'],$pos+1,1); ?>" style=" width: 24px; height: 24px" onerror="imageError(this,'<?=$coin['color']?>')"/>
                  <?php endif;
                   
                   ?>
              
               </a>
               <span class="amountCoin">
                   <?= round($coin['balance'],1)?>
                </span>
           </div>
        <?php endforeach;?>
        
        
    </div>
</div>
