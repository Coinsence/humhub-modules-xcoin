<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\xcoin\widgets;

use humhub\components\Widget;

/**
 * Description of SenderAccountField
 *
 * @author Luke
 */
class SenderAccountField extends Widget
{

    public $senderAccount;
    public $backRoute;

    public function run()
    {
        return $this->render('senderAccountField', ['backRoute' => $this->backRoute, 'senderAccount' => $this->senderAccount]);
    }

}
