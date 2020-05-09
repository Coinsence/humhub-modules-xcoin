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
    public $asset_id;

    /**
     * @var int[]
     */
    public $categories;

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

        if (empty($this->asset_id) && empty($this->categories) && empty($this->country) && empty($this->city) && empty($this->keywords)) {
            $this->addError('asset_id', Yii::t('XcoinModule.funding', 'At least one filter field must be filled'));

            return false;
        }

        if (empty($this->country) && !empty($this->city)) {
            $this->addError('country', Yii::t('XcoinModule.funding', 'Please select a country along the entered city'));

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
            [['asset_id'], 'integer'],
            [['categories', 'country', 'city', 'keywords'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'asset_id' => Yii::t('XcoinModule.base', 'Asset'),
            'categories' => Yii::t('XcoinModule.base', 'Categories'),
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
