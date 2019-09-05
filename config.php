<?php

use humhub\modules\space\models\Membership;
use humhub\modules\space\widgets\Menu;
use humhub\modules\user\models\forms\Registration;
use humhub\modules\user\widgets\ProfileMenu;
use humhub\widgets\TopMenu;
use humhub\modules\user\widgets\AccountTopMenu;

return [
    'id' => 'xcoin',
    'class' => 'humhub\modules\xcoin\Module',
    'namespace' => 'humhub\modules\xcoin',
    'events' => [
        ['class' => Menu::class, 'event' => Menu::EVENT_INIT, 'callback' => ['humhub\modules\xcoin\Events', 'onSpaceMenuInit']],
        ['class' => ProfileMenu::class, 'event' => ProfileMenu::EVENT_INIT, 'callback' => ['humhub\modules\xcoin\Events', 'onProfileMenuInit']],
        ['class' => TopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhub\modules\xcoin\Events', 'onTopMenuInit']],
        ['class' => AccountTopMenu::class, 'event' => TopMenu::EVENT_INIT, 'callback' => ['humhub\modules\xcoin\Events', 'onAccountTopMenuInit']],
        ['class' => Membership::class, 'event' => 'memberAdded', 'callback' => ['humhub\modules\xcoin\Events', 'onSpaceMemberAdd']],
        ['class' => Membership::class, 'event' => 'memberRemoved', 'callback' => ['humhub\modules\xcoin\Events', 'onSpaceMemberRemove']],
        ['class' => Registration::class, 'event' => Registration::EVENT_AFTER_REGISTRATION, 'callback' => ['humhub\modules\xcoin\Events', 'onUserRegistration']]
    ],
];

