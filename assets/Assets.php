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
    ];

    public $js = [
        'js/xcoin.js',
    ];
}
