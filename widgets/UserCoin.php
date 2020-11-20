<?php

namespace humhub\modules\xcoin\widgets;

use humhub\modules\xcoin\models\Account;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\models\AccountBalance;
use humhub\modules\xcoin\models\Transaction;
/**
 * UserTagsWidget lists all skills/tags of the user
 *
 * @package humhub.modules_core.user.widget
 * @since 0.5
 * @author andystrobel
 */
class UserCoin extends \yii\base\Widget
{
 
  public $assets=[];
  public    $listAssets=[];

 public function x(){
    $list = [];
    $account= new AccountBalance();
    
    foreach ($account->getAsset() as $asset) {
      
    }
  $listAssets=Transaction::find()->where(['to_account_id' => 289])->sum('amount');

  //print_r($listAssets);
    return $listAssets;
  }
  
    public $user;

    public function run()
    {
       
        return $this->render('userCoin', ['user' => $this->user,'list'=>$this->x()]);
    }

}

?>
