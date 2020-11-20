<?php

namespace humhub\modules\xcoin\widgets;
use humhub\modules\user\models\User;
use humhub\models\Setting;
use humhub\modules\mostactiveusers\models\ActiveUser;

class SidebarUser extends \humhub\components\Widget
{

    public function run()
    {
        $users = ActiveUser::find()->limit((int) Setting::Get('noUsers', 'mostactiveusers'))->all();
        if (count($users) == 0) {
            return;
        }
        $newUsers = User::find()->orderBy('created_at DESC')->visible();
        return $this->render('sidebarUser', array(
                    
                    'newUsers' => $newUsers,

        ));
    }

}

?>
