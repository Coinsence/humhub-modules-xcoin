<?php

namespace humhub\modules\xcoin\models;

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
    public $space;

    /**
     * @var int
     */
    public $challenge;

    /**
     * @var string
     */
    public $county;

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
        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'space' => Yii::t('XcoinModule.base', 'Space'),
            'challenge' => Yii::t('XcoinModule.base', 'Challenge'),
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
