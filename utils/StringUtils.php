<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2022 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\utils;

class StringUtils
{
    static function shorten($string, $length, $lastLength = 0, $trailingString = '...')
    {
        if (strlen($string) > $length) {
            $result = substr($string, 0, $length - $lastLength - strlen($trailingString)) . $trailingString;
            return $result . ($lastLength ? substr($string, -$lastLength) : '');
        }

        return $string;
    }
}