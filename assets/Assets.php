<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

namespace humhub\modules\xcoin\assets;

use yii\web\AssetBundle;

class Assets extends AssetBundle
{
    public $publishOptions = [
        'forceCopy' => true
    ];

    public $sourcePath = '@xcoin/resources';

    public $css = [
        'css/xcoin.css',
        'css/user.css',       
        'css/slick.css',
        'css/slick-theme.css',
       
    ];

    public $js = [
        'slick/slick.js',
        'js/xcoin.js',
        'js/sliders.js',
        'js/jquery-3.5.1.min.js',

    ];
}
