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
     * @var int the border radius of the image
     */
    public $borderRadius = 50;

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

        $imageHtmlOptions['style'] .= " width: " . $this->width . "px; height: " . $this->height . "px;";
        $imageHtmlOptions['style'] .= " border-radius: " . $this->borderRadius . "px;";

        $imageHtmlOptions['alt'] = Html::encode($this->category->name);
        
        return $this->render('@xcoin/widgets/views/category-image', [
            'category' => $this->category,
            'imageHtmlOptions' => $imageHtmlOptions
        ]);
    }
}
