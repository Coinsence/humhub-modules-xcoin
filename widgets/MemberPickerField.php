<?php

namespace humhub\modules\xcoin\widgets;

use humhub\modules\user\models\User;
use \humhub\modules\user\widgets\UserPickerField;

class MemberPickerField extends UserPickerField
{
    /**
     * @var models\Space
     */
    public $space;

    public function init() {

        $this->defaultRoute = $this->space->createUrl('/xcoin/member-search/json');

        $this->itemClass = User::class;

    }
}