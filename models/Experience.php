<?php

namespace humhub\modules\xcoin\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "xcoin_experience".
 *
 * @property integer $id
 * @property string $position
 * @property string $employer
 * @property string $description
 * @property string $country
 * @property string $city
 * @property string $start_date
 * @property string $end_date
 * @property integer $actual_position
 * @property integer $user_id
 *
 * @property User $user
 */
class Experience extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_experience';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['position', 'employer', 'start_date'], 'required'],
            [['end_date'], 'required', 'when' => function ($model) {
                return !$model->actual_position;
            }],
            ['end_date', 'validateDates'],
            [['user_id', 'actual_position'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => false, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['position', 'employer', 'description', 'country', 'city'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('XcoinModule.base', 'ID'),
            'position' => Yii::t('XcoinModule.base', 'Position'),
            'employer' => Yii::t('XcoinModule.base', 'Employer'),
            'description' => Yii::t('XcoinModule.base', 'Description'),
            'country' => Yii::t('XcoinModule.base', 'Country'),
            'city' => Yii::t('XcoinModule.base', 'City'),
            'start_date' => Yii::t('XcoinModule.base', 'Start Date'),
            'end_date' => Yii::t('XcoinModule.base', 'End Date'),
            'user_id' => Yii::t('XcoinModule.base', 'User ID'),
            'actual_position' => Yii::t('XcoinModule.base', 'Actual Position'),
        ];
    }

    public function validateDates()
    {
        if ($this->end_date) {
            if (strtotime($this->end_date) < strtotime($this->start_date)) {
                $this->addError('end_date', Yii::t('XcoinModule.experience', 'Experience Start Date must be greater than End Date'));
            }
        }
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->user_id = Yii::$app->user->id;
        }

        if ($this->actual_position) {
            $this->end_date = null;
        }

        if ($this->start_date) {
            $this->start_date = $this->start_date . '-01';
        }

        if ($this->end_date) {
            $this->end_date = $this->end_date . '-01';
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
