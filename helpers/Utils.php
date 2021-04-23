<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Mortadha Ghanmi <mortadha.ghanmi56@gmail.com>
 */

namespace humhub\modules\xcoin\helpers;


/**
 * Class Utils
 *
 * @author Mortadha Ghanmi <mortadha.ghanmi56@gmail.com>
 */
class Utils
{
    const TRANSACTION_PERIOD_NONE = -1;
    const TRANSACTION_PERIOD_WEEKLY = 0;
    const TRANSACTION_PERIOD_MONTHLY = 1;

    const SCHEDULE_DELAY_WEEKLY = 3600 * 24 * 7;
    const SCHEDULE_DELAY_MONTHLY = 3600 * 24 * 7 * 4;

    static function mempty()
    {
        foreach(func_get_args() as $arg)
            if(empty($arg))
                continue;
            else
                return false;
        return true;
    }
}
