<?php

use yii\helpers\Html;

use yii\helpers\Url;
use humhub\modules\friendship\models\Friendship;
humhub\modules\mostactiveusers\Assets::register($this);
?>
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

