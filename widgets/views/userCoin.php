<?php

use yii\helpers\Html;

use yii\helpers\Url;
/* 
*@var $users humhub\modules\user\models\User[] 
*/

use humhub\modules\xcoin\widgets\AssetAmount;
use humhub\modules\xcoin\widgets\SidebarUser as MostActiveUsers;
use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AccountHelper;

Assets::register($this);

use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
?>


<div class="userCoins">
    <div class="coinsHeader">
        <h2><span>Coins</span><i class="fa fa-question-circle"></i></h2>
        <a href="" class="accountDetail">Account Details</a>
    </div>
    <div class="coinsBody">
        <?php foreach($list as $lis) : ?>
           <div class="coin">
         <a class="tt"  title="" 
         data-toggle="tooltip" data-placement="top" 
         data-original-title="<?= $lis['name']?>"  style="border-radius: 50%;margin-right: 2px; background-color:<?=$lis['color']?>;">        
        
           <?php echo substr($lis['name'], 0, 1);?>
        
        </a>
            <span class="amountCoin"><?=$lis['sum(xcoin_transaction.amount)'] ?></span>
        </div>   
        <?php endforeach;?>
    </div>
</div>


 