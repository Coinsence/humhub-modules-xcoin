<?php


use humhub\libs\Iso3166Codes;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\ProfileOfferNeed;
use yii\helpers\Html;

/** @var $user User */
/** @var $profileOfferNeeds ProfileOfferNeed[] */
/** @var $htmlOptions [] */
?>


<div class="whatIOffer">
                
    <?php if (count($profileOfferNeeds) == 0) : ?>
    <p class="alert alert-warning col-md-12">   
    </p>
    <?php else : ?>
    <div class="whatWeOffer">
    <h2>What I offer to the community</h2>
    <p>
        <?php foreach ($profileOfferNeeds as $profileOfferNeed) : ?>

            <?= Html::encode($profileOfferNeed->profile_offer) ?>
            <?php if (!Yii::$app->user->isGuest) {
            echo Html::a(
                '<i class="fas fa-pencil-alt editPencil"></i>',
                [
                    '/xcoin/offer/edit','container' => $user
    
                    
                ],
                [
                    'data-target' => '#globalModal',
                    'class' => 'edit-btn'
                
                ]
            );
            }?>
        <?php endforeach; ?>
        
    </p>
    </div>
    
    <div class="whatWeNeed">
    <h2>What I need from the community</h2>
    <p>
        <?php foreach ($profileOfferNeeds as $profileOfferNeed) : ?>

            <?= Html::encode($profileOfferNeed->profile_need) ?>
            <?php if (!Yii::$app->user->isGuest) {
            echo Html::a(
                '<i class="fas fa-pencil-alt editPencil"></i>',
                [
                    '/xcoin/offer/edit','container' => $user
                    
                ],
                [
                    'data-target' => '#globalModal',
                    'class' => 'edit-btn'
                
                ]
            );
        }?>
        <?php endforeach; ?>
    </p>
    </div>
    <?php endif; ?>
</div>

    
   
       
 
  


