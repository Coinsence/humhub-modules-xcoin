<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\xcoin\widgets;

use humhub\modules\space\widgets\Image;

/**
 * Description of AssetImage
 *
 * @author Luke
 */
class AssetImage extends Image
{

    /**
     * @inheritdoc
     */
    public $showTooltip = true;

    /**
     * @inheritdoc
     */
    public $link = true;

    /**
     * @var \humhub\modules\xcoin\models\Asset
     */
    public $asset;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->asset !== null) {
            $this->space = $this->asset->space;
        }

        parent::init();
    }

}
