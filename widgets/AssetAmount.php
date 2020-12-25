<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\xcoin\widgets;

use humhub\components\Widget;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Asset;
use Yii;

/**
 * Description of SenderAccountField
 *
 * @author Luke
 */
class AssetAmount extends Widget
{

    public $holder;
    public $user_id;

    public function init() {

        if (!array_key_exists('defaultAssetName', Yii::$app->params))
            return;

        if (Yii::$app->user->identity === null)
            return;

        if (!isset($this->holder))
            $this->holder = Yii::$app->params['defaultAssetName'];

        $this->user_id = Yii::$app->user->identity->id;

        parent::init();

    }

    public function run()
    {
        $space = Space::findOne([
            'name' => $this->holder
        ]);

        $identity = Yii::$app->user->identity;

        if ($identity === null)
            return;

        $user = User::findIdentity($identity->id);

        if ($space === null)
            return;

            return $this->render('assetAmount', ['holder' => $this->holder, 'amount' => $this->getAssetAmount(), 'space' => $space, 'user' => $user]);
    }

    public function getAssetAmount() {

       $space = Space::findOne([
           'name' => $this->holder
       ]);

       if ($space === null)
           return;

       $asset = Asset::findOne([
           'space_id' => $space->id
       ]);

        if ($asset === null)
            return;

        $sum = 0;

       foreach (Account::findAll(['user_id' => $this->user_id]) as $account ) {
           $sum += $account->getAssetBalance($asset);
       }

        return $sum;

    }

}
