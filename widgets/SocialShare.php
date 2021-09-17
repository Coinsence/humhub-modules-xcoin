<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Mortadha Ghanmi <mortadha.ghanmi56@gmail.com>
 */

namespace humhub\modules\xcoin\widgets;

use humhub\components\Widget;

/**
 * Returns Social share buttons
 */
class SocialShare extends Widget
{
    public $url;

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!$this->url || $this->url === '')
            return;

        return $this->render('@xcoin/widgets/views/social-share', [
            'url' => $this->url,
        ]);
    }
}
