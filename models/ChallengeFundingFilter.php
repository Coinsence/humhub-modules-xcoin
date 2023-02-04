<?php

namespace humhub\modules\xcoin\models;

use humhub\modules\xcoin\helpers\Utils;
use Yii;
use yii\base\Model;


/**
 * Class ChallengeFundingFilter
 * @package humhub\modules\xcoin\models
 *
 */
class ChallengeFundingFilter extends Model
{

    /**
     * @var int[]
     */
    public $category;

    /**
     * @var string
     */
    public $country;

    /**
     * @var string
     */
    public $city;

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        parent::beforeValidate();

        if (empty($this->country) && !empty($this->city)) {
            $this->addError('country', Yii::t('XcoinModule.challenge', 'Please select a country along the entered city'));

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
            [['category', 'country', 'city'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category' => Yii::t('XcoinModule.base', 'Categories'),
            'country' => Yii::t('XcoinModule.base', 'Country'),
            'city' => Yii::t('XcoinModule.base', 'City'),
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
