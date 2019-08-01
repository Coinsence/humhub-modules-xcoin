<?php

namespace humhub\modules\xcoin\helpers;

use humhub\modules\space\models\Space;
use humhub\modules\xcoin\models\Account;
use Yii;

/**
 * SpaceHelper
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */
class SpaceHelper
{
    public static function generateRandomSpaceName($length = 10)
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }

    public static function allowDirectCoinTransfer(Account $account)
    {
        if (!$account->space_id)
            return null;

        $space = Space::findOne(['id' => $account->space_id]);
        $module = Yii::$app->getModule('xcoin');

        return $module->settings->contentContainer($space)->get('allowDirectCoinTransfer');
    }
}
