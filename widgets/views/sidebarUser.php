<?php

use yii\helpers\Html;

use yii\helpers\Url;
use humhub\modules\friendship\models\Friendship;
humhub\modules\mostactiveusers\Assets::register($this);
?>
<link
      href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap"
      rel="stylesheet"
    />

    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
      integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2"
      crossorigin="anonymous"
    />
<link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
      integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA=="
      crossorigin="anonymous"
    />
<link rel="stylesheet" href="/var/www/test/humhub/themes/Coinsence/css/userInfo.css" />
    <link rel="stylesheet" type="text/css" href="/var/www/test/humhub/themes/Coinsence/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="/var/www/test/humhub/themes/Coinsence/slick/slick-theme.css" />
<div class="panel panel-default" id="mostactiveusers-panel">

    <!-- Display panel menu widget -->
    <?php humhub\widgets\PanelMenu::widget(array('id' => 'mostactiveusers-panel')); ?>
<div  class="peopleMayLike">
    <div class="panel-heading">
    
        <?php echo Yii::t('MostactiveusersModule.base', '<h1><strong>People</strong>  you might know</h1>'); ?>
    </div>
    <div class="panel-body">
        <?php
        // run through the array of users
        foreach ($newUsers->limit(3)->all() as $user)  {
            ?>
        <div class="personMayLike">
              <div>
                <img
                  class="personProfilePicture"
                  src="<?php echo $user->getProfileImage()->getUrl(); ?>"
                  alt=""
                  data-original-title="<?php echo Html::encode($user->displayName); ?>"
                />
                <div class="personInfo">
                  <h2 class="personName"><?= Html::encode($user->displayName); ?></h2>
                  <h2 class="personPost"><?= Html::encode($user->profile->title); ?></h2>
                </div>
              </div>
              <div class="">
              <?php

                  if (!Yii::$app->user->isGuest && !$user->isCurrentUser() && Yii::$app->getModule('friendship')->getIsEnabled()) {
                      $friendShipState = Friendship::getStateForUser(Yii::$app->user->getIdentity(), $user);
                      if ($friendShipState === Friendship::STATE_NONE) {
                      echo Html::a('<img src="/themes/Coinsence/img/addPerson.svg" alt="" />' . Yii::t("FriendshipModule.base", ""), Url::to(['/friendship/request/add', 'userId' => $user->id]), ['class' => 'addPerson', 'data-method' => 'POST', 'data-ui-loader' => '']); 
                          }
                    //   else{
                    //    echo Html::a('<img src="/themes/Coinsence/img/addPerson.svg" alt="" />'.Yii::t("FriendshipModule.base", ""), Url::to(['/friendship/request/delete', 'userId' => $user->id]), ['class' => 'btn btn-default zouaoui', 'data-method' => 'POST', 'data-ui-loader' => '']);
                    //   }
                  }
              ?>
             

              </div>
        </div>
           

            <?php
        }

       

       
        ?>
    </div>
    </div>
</div>

