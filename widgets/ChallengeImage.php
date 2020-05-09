<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\widgets;

use humhub\modules\xcoin\models\Challenge;
use yii\base\Widget;
use yii\bootstrap\Html;

/**
 * Return challenge image
 */
class ChallengeImage extends Widget
{

    /**
     * @var Challenge
     */
    public $challenge;

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
     * @var boolean create link to the challenge
     */
    public $link = false;

    /**
     * @var boolean create text title of the challenge
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
            $this->linkOptions['href'] = $this->challenge->space->createUrl('/xcoin/challenge/overview', [
                'challengeId' => $this->challenge->id
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

        $imageHtmlOptions['alt'] = Html::encode($this->challenge->title);
        
        return $this->render('@xcoin/widgets/views/challenge-image', [
            'challenge' => $this->challenge,
            'link' => $this->link,
            'title' => $this->title,
            'linkOptions' => $this->linkOptions,
            'imageHtmlOptions' => $imageHtmlOptions,
        ]);
    }
}
