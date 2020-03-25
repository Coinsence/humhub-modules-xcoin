<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\widgets;

use humhub\modules\xcoin\models\Category;
use yii\base\Widget;
use yii\bootstrap\Html;

/**
 * Return category image
 */
class CategoryImage extends Widget
{

    /**
     * @var Category
     */
    public $category;

    /**
     * @var int the width of the image
     */
    public $width = 50;

    /**
     * @var int the height of the image
     */
    public $height = null;

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

        if (!isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = '';
        }

        if (!isset($this->htmlOptions['style'])) {
            $this->htmlOptions['style'] = '';
        }

        $imageHtmlOptions = $this->htmlOptions;

        $imageHtmlOptions['style'] .= " width: " . $this->width . "px; height: " . $this->height . "px";

        $imageHtmlOptions['alt'] = Html::encode($this->category->name);
        
        return $this->render('@xcoin/widgets/views/image', [
            'category' => $this->category,
            'imageHtmlOptions' => $imageHtmlOptions
        ]);
    }

    protected function getDynamicStyles($elementWidth)
    {

        $fontSize = 44 * $elementWidth / 100;
        $padding = 18 * $elementWidth / 100;
        $borderRadius = $elementWidth / 2;

        return "font-size: " . $fontSize . "px; padding: " . $padding . "px 0; border-radius: " . $borderRadius . "px;";
    }

}
