<?php

namespace humhub\modules\xcoin\helpers;

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
}
