<?php

namespace humhub\modules\xcoin;

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Funding;
use humhub\widgets\TopMenu;
use Yii;
use yii\base\Event;
use yii\helpers\Url;

class Events
{

    public static function onTopMenuInit($event)
    {
        $event->sender->addItem([
            'label' => Yii::t('XcoinModule.base', 'Crowd Funding'),
            'url' => Url::to(['/xcoin/funding-overview']),
            'icon' => '<i class="fa fa-leaf"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id == 'funding-overview'),
            'sortOrder' => 900,
        ]);

        $event->sender->addItem([
            'label' => Yii::t('XcoinModule.base', 'Marketplace'),
            'url' => Url::to(['/xcoin/marketplace']),
            'icon' => '<i class="fa fa-shopping-basket"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id == 'marketplace'),
            'sortOrder' => 900,
        ]);

        Event::on(TopMenu::class, TopMenu::EVENT_BEFORE_RUN, function ($event) {
            // deactivate directory menu item
            $event->sender->deleteItemByUrl(Url::to(['/directory/directory']));
        });


        /*
        $event->sender->addItem([
            'label' => Yii::t('XcoinModule.base', 'Asset Exchange'),
            'url' => Url::to(['/xcoin/exchange']),
            'icon' => '<i class="fa fa-exchange"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id == 'exchange'),
            'sortOrder' => 100000,
        ]);

        $event->sender->addItem([
            'label' => Yii::t('XcoinModule.base', 'Jobs / Help wanted'),
            'url' => Url::to(['/xcoin/job/overview']),
            'icon' => '<i class="fa fa-gavel"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id == 'job' && Yii::$app->controller->action->id == 'overview'),
            'sortOrder' => 900,
        ]);
        */
    }

    public static function onSpaceMenuInit($event)
    {
        $space = $event->sender->space;
        if ($space->isModuleEnabled('xcoin')) {

            if (AssetHelper::canManageAssets($space) || Funding::find()->where(['space_id' => $space->id])->andWhere(['>', 'available_amount', 0])->count() > 0) {
                $event->sender->addItem([
                    'label' => Yii::t('XcoinModule.base', 'Crowd Funding'),
                    'url' => $space->createUrl('/xcoin/funding'),
                    'icon' => '<i class="fa fa-leaf"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'funding'),
                    'sortOrder' => 10000,
                ]);
            }

            $event->sender->addItem([
                'label' => Yii::t('XcoinModule.base', 'Accounts'),
                'url' => $space->createUrl('/xcoin/overview'),
                'icon' => '<i class="fa fa-money"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && !in_array(Yii::$app->controller->id, ['funding', 'product', 'ethereum'])),
            ]);

            $event->sender->addItem([
                'label' => Yii::t('XcoinModule.base', 'Products'),
                'url' => $space->createUrl('/xcoin/product'),
                'icon' => '<i class="fa fa-product-hunt"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'product'),
                'sortOrder' => 20000,
            ]);

            if ($space->dao_address && $space->coin_address) {

                // used to include ether-icon since it's not present in fontawesome 4.7.0 icons
                Assets::register(Yii::$app->view);

                $event->sender->addItem([
                    'label' => Yii::t('XcoinModule.base', 'Ethereum'),
                    'htmlOptions' => ['class' => 'pt-3'],
                    'url' => $space->createUrl('/xcoin/ethereum'),
                    'icon' => '<i class="ether-icon-menu"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'ethereum'),
                    'sortOrder' => 30000,
                ]);
            }
        }
    }

    public static function onProfileMenuInit($event)
    {
        $user = $event->sender->user;

        if ($user->isModuleEnabled('xcoin')) {
            $event->sender->addItem([
                'label' => Yii::t('XcoinModule.base', 'Accounts'),
                'url' => $user->createUrl('/xcoin/overview'),
                'icon' => '<i class="fa fa-money"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'overview'),
            ]);

            $event->sender->addItem([
                'label' => Yii::t('XcoinModule.base', 'Products'),
                'url' => $user->createUrl('/xcoin/product'),
                'icon' => '<i class="fa fa-product-hunt"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'product'),
            ]);
        }
    }

    public static function onAccountTopMenuInit($event)
    {
        $user = Yii::$app->user->getIdentity();

        if ($user->isModuleEnabled('xcoin')) {

            $event->sender->addItem(array(
                'label' => '---',
                'url' => '#',
                'sortOrder' => 205,
            ));

            $event->sender->addItem([
                'label' => Yii::t('XcoinModule.base', 'Accounts'),
                'url' => $user->createUrl('/xcoin/overview'),
                'icon' => '<i class="fa fa-money"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'overview'),
                'sortOrder' => 210,
            ]);

            $event->sender->addItem([
                'label' => Yii::t('XcoinModule.base', 'Products'),
                'url' => $user->createUrl('/xcoin/product'),
                'icon' => '<i class="fa fa-product-hunt"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'product'),
                'sortOrder' => 220,
            ]);
        }
    }

}
