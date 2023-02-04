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
    public $location;

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
            [['category', 'location'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'category' => Yii::t('XcoinModule.base', 'Category'),
            'location' => Yii::t('XcoinModule.base', 'Location'),
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
