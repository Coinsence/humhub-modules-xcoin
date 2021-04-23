<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\widgets;

use humhub\modules\xcoin\models\Marketplace;
use yii\base\Widget;
use yii\bootstrap\Html;

/**
 * Return marketplace image
 */
class MarketplaceImage extends Widget
{

    /**
     * @var Marketplace
     */
    public $marketplace;

    /**
     * @var int the width of the image
     */
    public $width = 50;

    /**
     * @var int the height of the image
     */
    public $height = null;

    /**
     * @var int the border radius of the image
     */
    public $borderRadius = 50;

    /**
     * @var boolean create link to the marketplace
     */
    public $link = false;

    /**
     * @var boolean create text title of the marketplace
     */
    public $title = true;

    /**
     * @var array Html Options of the link
     */
    public $linkOptions = [];

    /**
     * @var array html options for the generated tag
     */
    public $htmlOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->height === null) {
            $this->height = $this->width;
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!isset($this->linkOptions['href'])) {
            $this->linkOptions['href'] = $this->marketplace->space->createUrl('/xcoin/marketplace/overview', [
                'marketplaceId' => $this->marketplace->id
            ]);
        }

        if (!isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = '';
        }

        if (!isset($this->htmlOptions['style'])) {
            $this->htmlOptions['style'] = '';
        }

        $imageHtmlOptions = $this->htmlOptions;

        $imageHtmlOptions['style'] .= " width: " . $this->width . "px; height: " . $this->height . "px;";
        $imageHtmlOptions['style'] .= " border-radius: " . $this->borderRadius . "px;";

        $imageHtmlOptions['alt'] = Html::encode($this->marketplace->title);
        
        return $this->render('@xcoin/widgets/views/marketplace-image', [
            'marketplace' => $this->marketplace,
            'link' => $this->link,
            'title' => $this->title,
            'linkOptions' => $this->linkOptions,
            'imageHtmlOptions' => $imageHtmlOptions,
        ]);
    }
}
