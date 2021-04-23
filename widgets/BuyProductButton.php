<?php

namespace humhub\modules\xcoin\widgets;

use humhub\widgets\ModalButton;
use Yii;
use humhub\components\Widget;
use humhub\libs\Html;

class BuyProductButton extends Widget
{

    /**
     * @var string
     */
    public $guid;
    
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $size = 'sm';

    /**
     * @var string
     */
    public $icon = 'fa-plus';

    /**
     * @var string
     */
    public $label;


    /**
     * Creates the Wall Widget
     */
    public function run()
    {
        $button = ModalButton::none($this->getLabel())->load([
            '/mail/mail/create',
            'ajax' => 1,
            'userGuid' => $this->guid
        ])->cssClass('btn-invest');
       
       
        return $button;
    }

    public function getLabel()
    {
        return $this->label;
    }
}
