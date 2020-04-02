<?php

namespace humhub\modules\xcoin\models;

use humhub\modules\xcoin\helpers\Utils;
use Yii;
use yii\base\Model;


/**
 * Class FundingFilter
 * @package humhub\modules\xcoin\models
 *
 */
class FundingFilter extends Model
{

    /**
     * @var int
     */
    public $space_id;

    /**
     * @var int
     */
    public $challenge_id;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $city;

    /**
     * @var string
     */
    public $keywords;


    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        parent::beforeValidate();

        if (empty($this->space_id) && empty($this->challenge_id) && empty($this->country) && empty($this->city) && empty($this->keywords)) {
            $this->addError('space_id', Yii::t('XcoinModule.funding', 'At least one filter field must be filled'));

            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['space_id', 'challenge_id'], 'integer'],
            [['country', 'city', 'keywords'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'space_id' => Yii::t('XcoinModule.base', 'Space'),
            'challenge_id' => Yii::t('XcoinModule.base', 'Challenge'),
            'country' => Yii::t('XcoinModule.base', 'Country'),
            'city' => Yii::t('XcoinModule.base', 'City'),
            'keywords' => Yii::t('XcoinModule.base', 'Keywords')
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return [];
    }

}
