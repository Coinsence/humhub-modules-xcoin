<?php

use humhub\modules\user\models\User;
use humhub\modules\xcoin\assets\Assets;
use yii\helpers\Url;

Assets::register($this);

/* @var $user User */
/* @var $coins array */
/* @var $spaces array */
/* @var $cssClass string */

?>

<div class="userCoins <?= $cssClass ?>">
    <div class="coinsHeader">
    <h2><?= Yii::t('XcoinModule.account', 'Coins') ?></h2>
            
        
        <a href="<?= $user->createUrl('/xcoin/overview') ?>" class="accountDetail"><?= Yii::t('XcoinModule.account','Account Details')?></a>
    </div>
    <div class="coinsBody">
        <?php if($coins==null) :?>
            <span><?= Yii::t('XcoinModule.account', 'No Coins found')?>.<span>
        <?php else: ?>
            <?php foreach($coins as $coin) : ?>
                <div class="coin">
               <?php $url=$coin['url']; ?>
               <a class="tt myCoinBlock" title=""
                  data-toggle="tooltip" data-placement="top"
                  data-original-title="<?= $coin['name'] ?>"
                  href="<?= Url::to(['/space/'.$url])?>"
               >
                   <?php
                   $pos=strpos($coin['name'], ' ');

                   if($pos!=false):

                       ?>
                       <img class="current-space-image space-profile-image-<?=$coin['id']?> img-rounded profile-user-photo" src="/uploads/profile_image/<?=$coin['guid']?>.jpg?m=1606922234" alt="<?=substr($coin['name'],0,1).substr($coin['name'],$pos+1,1); ?>" style=" width: 24px; height: 24px" onerror="imageError(this,'<?=$coin['color']?>')"/>

                   <?php endif;
                   ?>
               </a>
               <span class="amountCoin">
                   <?= round($coin['balance'],1)?>
                </span>
           </div>
            <?php endforeach;?>
        <?php endif;?>

    </div>
</div>
