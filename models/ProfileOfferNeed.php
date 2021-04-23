<?php

namespace humhub\modules\xcoin\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "profile_offer_need".
 *
 * @property integer $id
 * @property string $profile_offer
 * @property string $profile_need
 * @property integer $user_id
 *
 * @property User $user
 */
class ProfileOfferNeed extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'profile_offer_need';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => false, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['profile_offer', 'profile_need'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('XcoinModule.base', 'ID'),
            'profile_need' => Yii::t('XcoinModule.base', 'Profile Need'),
            'profile_offer' => Yii::t('XcoinModule.base', 'Profile Offer'),
            'user_id' => Yii::t('XcoinModule.base', 'User ID'),
            
        ];
    }

    
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->user_id = Yii::$app->user->id;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
