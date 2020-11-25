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
?>



<div class="userCoins">
    <div class="coinsHeader">
        <h2><span>Coins</span><i class="fa fa-question-circle"></i></h2>
        <a href="" class="accountDetail">Account Details</a>
    </div>
    <div class="coinsBody">

        <div class="coin">
        <div class="panel-body">
    </div>
            <img src="/themes/Coinsence/img/coinsenceToken.jpg" class="coinImage" alt="" />
            <span class="amountCoin">500</span>
        </div>
        <div class="coin">
            <img src="/themes/Coinsence/img/coinsenceToken.jpg" class="coinImage" alt="" />
            <span class="amountCoin">500</span>
        </div>
        <div class="coin">
            <img src="/themes/Coinsence/img/coinsenceToken.jpg" class="coinImage" alt="" />
            <span class="amountCoin">500</span>
        </div>
        <div class="coin">
            <img src="/themes/Coinsence/img/coinsenceToken.jpg" class="coinImage" alt="" />
            <span class="amountCoin">500</span>
        </div>
    </div>
</div>
<div class="ongoingprojects">
    <h2>Ongoing Projects you might like</h2>
    <div class="ongoingproject">
        <img src="/themes/Coinsence/img/project5.jpg" alt="" />
        <p>Coinsence Tunisia Community Building</p>
    </div>
    <div class="ongoingproject">
        <img src="/themes/Coinsence/img/project5.jpg" alt="" />
        <p>Vernissage de l'exposition LES ARTISTES de LA MÉDINA</p>
    </div>
    <div class="ongoingproject">
        <img src="/themes/Coinsence/img/project5.jpg" alt="" />
        <p>
            La présentation du dernier essai de Mr HAKIM BEN HAMOUDA (ancien
            ministre de l'économie et des
        </p>
    </div>
</div>
<div class="offersMayLike">
    <h2>Offers you might like</h2>
    <div class="offerMayLike">
        <img src="/themes/Coinsence/img/project4.jpg" alt="" />
        <p>Learn everything about photography in this 2 days course</p>
    </div>
    <div class="offerMayLike">
        <img src="/themes/Coinsence/img/project4.jpg" alt="" />
        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
    </div>
    <div class="offerMayLike">
        <img src="/themes/Coinsence/img/project4.jpg" alt="" />
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            Sagittis duis maecenas facilisi enim justo.
        </p>
    </div>
</div>

<?=MostActiveUsers::widget();?>
<?=AssetAmount::widget();?>
<?php 