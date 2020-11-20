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
<link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
    integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
    integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
    crossorigin="anonymous" />


<div class="userCoins">
    <div class="coinsHeader">
        <h2><span>Coins</span><i class="far fa-question-circle"></i></h2>
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