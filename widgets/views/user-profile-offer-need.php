<?php


use humhub\libs\Iso3166Codes;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\ProfileOfferNeed;
use yii\helpers\Html;

/** @var $user User */
/** @var $profileOfferNeeds ProfileOfferNeed[] */
/** @var $htmlOptions [] */
?>

<?php if (count($profileOfferNeeds) == 0) : ?>
   
<?php else : ?>
    <div class="whatIOffer">
                    
    
    
        
        <div class="whatWeOffer">
        <h2>What I offer to the community</h2>
        <?php if (!Yii::$app->user->isGuest) {
                echo Html::a(
                    '<i class="fa fa-pencil editPencil"></i>',
                    [
                        '/xcoin/offer/edit','container' => $user,'id'=>$user->id
        
                        
                    ],
                    [
                        'data-target' => '#globalModal',
                        'class' => 'edit-btn'
                    
                    ]
                );
                }?>
        <p>
            <?php foreach ($profileOfferNeeds as $profileOfferNeed) : ?>

                <?= Html::encode($profileOfferNeed->profile_offer) ?>
                
            <?php endforeach; ?>
            
        </p>
        </div>
        
    
        <div class="whatWeNeed">
        <h2>What I need from the community</h2>
        <?php if (!Yii::$app->user->isGuest) {
                echo Html::a(
                    '<i class="fa fa-pencil editPencil"></i>',
                    [
                        '/xcoin/need/edit','container' => $user
                        
                    ],
                    [
                        'data-target' => '#globalModal',
                        'class' => 'edit-btn'
                    
                    ]
                );
            }?>
        <p>
            <?php foreach ($profileOfferNeeds as $profileOfferNeed) : ?>

                <?= Html::encode($profileOfferNeed->profile_need) ?>
            
            <?php endforeach; ?>
        </p>
        </div>
    
    </div>
<?php endif; ?>

    
   
       
 
  


