<?php

namespace humhub\modules\xcoin\widgets;

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
     * @var string css classes
     */
    public $cssClass;
 
    public function run()
    {
        $dbCommand = Yii::$app->db->createCommand("
            select space.name,space.color,sum(xcoin_transaction.amount) from xcoin_transaction
            left join xcoin_account on xcoin_transaction.to_account_id=xcoin_account.id 
            left join xcoin_asset on xcoin_asset.id=xcoin_transaction.asset_id 
            left join space on xcoin_asset.space_id=space.id 
            where xcoin_account.user_id=". $this->user->id ." GROUP BY space.name");
        $data = $dbCommand->queryAll();//output
        $spaces=[];
        foreach($data as $d) {
            $spaceCommAND=Yii::$app->db->createCommand("select * as spaces from space where name=".$d['name']);
            array_push($spaces,$spaceCommAND);
        }
        return $this->render('userCoin', ['user' => $this->user, 'assets' => $data, 'spaces' => $spaces, 'cssClass' => $this->cssClass]);
    }
}

?>
