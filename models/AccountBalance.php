<?php

namespace humhub\modules\xcoin\models;

use Yii;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Asset;
use humhub\modules\xcoin\models\Account;

/**
 * This is the model class for view "xcoin_v_account_balance".
 *
 * @property integer $account_id
 * @property integer $asset_id
 * @property float $balance
 *
 */
class AccountBalance extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_v_account_balance';
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(Asset::class, ['id' => 'asset_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id']);
    }

}
