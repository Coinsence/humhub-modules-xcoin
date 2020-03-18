<?php

/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\models;

use humhub\components\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "xcoin_funding_category".
 *
 * @property integer $id
 * @property integer $funding_id
 * @property integer $category_id
 * @property Funding $funding
 * @property Category $category
 */
class FundingCategory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_funding_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['funding_id', 'category_id'], 'required'],
            [['funding_id', 'category_id'], 'integer'],
            [['funding_id', 'category_id'], 'unique', 'targetAttribute' => ['funding_id', 'category_id'], 'message' => 'The combination of Funding ID and Category ID has already been taken.']
        ];
    }

    /**
     * Returns related Funding
     *
     * @return ActiveQuery
     */
    public function getFunding()
    {
        return $this->hasOne(Funding::class, ['id' => 'funding_id']);
    }

    /**
     * Returns related Category
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }
}
