<?php

namespace humhub\modules\xcoin;

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Funding;
use humhub\modules\xcoin\models\Product;
use humhub\modules\xcoin\models\Transaction;
use humhub\widgets\TopMenu;
use Yii;
use yii\base\Event;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\HttpException;

class Events
{

    public static function onTopMenuInit($event)
    {
        $event->sender->addItem([
            'label' => Yii::t('XcoinModule.base', 'Crowd Funding'),
            'url' => Url::to(['/xcoin/funding-overview', 'verified' => Funding::FUNDING_REVIEWED]),
            'icon' => '<i class="fa fa-leaf"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id == 'funding-overview'),
            'sortOrder' => 900,
        ]);

        $event->sender->addItem([
            'label' => Yii::t('XcoinModule.base', 'Marketplace'),
            'url' => Url::to(['/xcoin/marketplace', 'verified' => Product::PRODUCT_REVIEWED]),
            'icon' => '<i class="fa fa-shopping-basket"></i>',
            'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id == 'marketplace'),
            'sortOrder' => 900,
        ]);

        Event::on(TopMenu::class, TopMenu::EVENT_BEFORE_RUN, function ($event) {
            // deactivate directory menu item
            $event->sender->deleteItemByUrl(Url::to(['/directory/directory']));
        });
    }

    public static function onSpaceMenuInit($event)
    {
        $space = $event->sender->space;

        if ($space->isModuleEnabled('xcoin')) {

            // used to include ether-icon since it's not present in fontawesome 4.7.0 icons
            Assets::register(Yii::$app->view);

            $event->sender->addItem([
                'label' => Yii::t('XcoinModule.base', 'Accounts'),
                'url' => $space->createUrl('/xcoin/overview'),
                'icon' => '<i class="fa fa-money"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && !in_array(Yii::$app->controller->id, ['funding', 'product', 'ethereum', 'challenge'])),
            ]);

            $event->sender->addItem([
                'label' => Yii::t('XcoinModule.base', 'Products'),
                'url' => $space->createUrl('/xcoin/product'),
                'icon' => '<i class="fa fa-product-hunt"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'product'),
                'sortOrder' => 10000,
            ]);

            $event->sender->addItem([
                'label' => Yii::t('XcoinModule.base', 'Ethereum'),
                'url' => $space->createUrl('/xcoin/ethereum'),
                'icon' => '<i class="ether-icon-menu"></i>',
                'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'ethereum'),
                'sortOrder' => 20000,
            ]);


            if (AssetHelper::canManageAssets($space) || Funding::find()->count() > 0) {
                $event->sender->addItemGroup([
                    'id' => 'crowdfunding',
                    'label' => Yii::t('SpaceModule.widgets_SpaceMenuWidget',  Yii::t('XcoinModule.base', 'Crowdfunding')),
                    'sortOrder' => 30000,
                ]);
                $event->sender->addItem([
                    'label' => Yii::t('XcoinModule.base', 'Space Challenges'),
                    'group' => 'crowdfunding',
                    'url' => $space->createUrl('/xcoin/challenge'),
                    'icon' => '<i class="fa fa-users"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'challenge'),
                    'sortOrder' => 30000,
                ]);
                $event->sender->addItem([
                    'label' => Yii::t('XcoinModule.base', 'Submitted Proposals'),
                    'group' => 'crowdfunding',
                    'url' => $space->createUrl('/xcoin/funding'),
                    'icon' => '<i class="fa fa-leaf"></i>',
                    'isActive' => (Yii::$app->controller->module && Yii::$app->controller->module->id == 'xcoin' && Yii::$app->controller->id === 'funding'),
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

    /**
     * When a members joins a space for the first time an account TYPE_COMMUNITY_INVESTOR
     * is created and credited by an amount specified by the admin.
     *
     * @param Event
     * @throws HttpException
     * @throws Exception
     */
    public static function onSpaceMemberAdd($event)
    {
        // Get new account transaction parameters
        $module = Yii::$app->getModule('xcoin');

        if (!$module->settings->space()) {
            return;
        }

        $accountTitle = $module->settings->space()->get('accountTitle');
        $transactionAmount = $module->settings->space()->get('transactionAmount');
        $transactionComment = $module->settings->space()->get('transactionComment');

        //Prepare accounts
        $space = $event->space;
        $user = $event->user;

        $spaceIssueAccount = AccountHelper::getIssueAccount($space);
        $spaceDefaultAccount = Account::findOne(['space_id' => $space->id, 'account_type' => Account::TYPE_DEFAULT]);

        //Exit if module settings are not set or space default account or issue account are not set
        if (!$accountTitle || !$transactionAmount || !$transactionComment || !$spaceIssueAccount || !$spaceDefaultAccount) {
            return;
        }

        $memberAccount = Account::findOne([
            'investor_id' => $user->id,
            'space_id' => $space->id,
            'account_type' => Account::TYPE_COMMUNITY_INVESTOR
        ]);

        if ($memberAccount) {
            $memberAccount->updateAttributes(['user_id' => $user->id]);

            return;
        }

        $memberAccount = new Account();
        $memberAccount->space_id = $space->id;
        $memberAccount->user_id = $user->id;
        $memberAccount->title = $accountTitle;
        $memberAccount->account_type = Account::TYPE_COMMUNITY_INVESTOR;
        $memberAccount->investor_id = $user->id;
        if (!$memberAccount->save()) {
            throw new Exception('Could not create member account!');
        }

        //Issue transaction amount to default account
        $issueTransaction = new Transaction();
        $issueTransaction->amount = $transactionAmount;
        $issueTransaction->from_account_id = $spaceIssueAccount->id;
        $issueTransaction->to_account_id = $spaceDefaultAccount->id;
        $issueTransaction->asset_id = AssetHelper::getSpaceAsset($space)->id;
        $issueTransaction->transaction_type = Transaction::TRANSACTION_TYPE_ISSUE;
        $issueTransaction->comment = "Issue transaction Amount to default Account";
        if (!$issueTransaction->save()) {
            throw new HttpException(500, "can't issue this Amount !");
        }

        //New member account transaction
        $transferTransaction = new Transaction();
        $transferTransaction->amount = $transactionAmount;
        $transferTransaction->from_account_id = $spaceDefaultAccount->id;
        $transferTransaction->to_account_id = $memberAccount->id;
        $transferTransaction->asset_id = AssetHelper::getSpaceAsset($space)->id;
        $transferTransaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
        $transferTransaction->comment = $transactionComment;
        if (!$transferTransaction->save()) {
            throw new HttpException(500, "Can't transfer transaction amount to member account");
        }
    }

    /**
     * When a members leaves leaving an TYPE_COMMUNITY_INVESTOR account , he will be
     * removed from managing this account.
     *
     * @param Event
     */
    public static function onSpaceMemberRemove($event)
    {
        $space = $event->space;
        $user = $event->user;

        $memberAccount = Account::findOne([
            'investor_id' => $user->id,
            'space_id' => $space->id,
            'account_type' => Account::TYPE_COMMUNITY_INVESTOR
        ]);

        if (!$memberAccount) {
            return;
        }

        $memberAccount->updateAttributes(['user_id' => null]);
    }

    /**
     * Creating account type DEFAULT for new registered user
     *
     * @param Event
     */
    public static function onUserRegistration($event)
    {
        $user = $event->identity;

        AccountHelper::initContentContainer($user);
    }
}
