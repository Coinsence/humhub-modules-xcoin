<?php

namespace humhub\modules\xcoin\widgets;
use humhub\modules\like\models\Like;
use humhub\modules\user\models\User;
use Yii;

/**
 * UserTagsWidget lists all skills/tags of the user
 *
 * @package humhub.modules_core.user.widget
 * @since 0.5
 * @author andystrobel
 */
class UserCoin extends \yii\base\Widget
{
    /**
     * @var User user
     */
    public $user;

    /**
     * @var \humhub\modules\space\models\Space the Space which this header belongs to
     */
    public $space;
    /**
     * @var string css classes
     */
    public $cssClass;
 
    public function run()
    {
        $dbCommand = Yii::$app->db->createCommand("
        select xcoin_v_account_balance.balance,space.*
         from xcoin_v_account_balance 
        left join xcoin_account on xcoin_account.id=xcoin_v_account_balance.account_id left join xcoin_asset on xcoin_asset.id=xcoin_v_account_balance.asset_id 
        left join space on space.id=xcoin_asset.space_id 
        where xcoin_account.user_id=". $this->user->id." and xcoin_v_account_balance.balance !=0;");
        $data = $dbCommand->queryAll();//output
        $spaces=[];
       //$listlike= Like::find()->where(['created_by'=>13])->all();
       //print_r($listlike);
        foreach($data as $d) {
            $spaceCommAND=Yii::$app->db->createCommand("select *  from space where url='".$d['url']."';");
            $x=$spaceCommAND->queryAll();
            array_push($spaces,$x);
        }
        return $this->render('userCoin', [
            'user' => $this->user, 
            'coins' => $data,
            'spaces' => $spaces,
            'cssClass' => $this->cssClass
            ]);
    }
}

?>
