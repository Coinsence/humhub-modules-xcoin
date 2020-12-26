<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\models;

use humhub\components\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "xcoin_marketplace_category".
 *
 * @property integer $id
 * @property integer $marketplace_id
 * @property integer $category_id
 * @property Marketplace $marketplace
 * @property Category $category
 */
class MarketplaceCategory extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_marketplace_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['marketplace_id', 'category_id'], 'required'],
            [['marketplace_id', 'category_id'], 'integer'],
            [['marketplace_id', 'category_id'], 'unique', 'targetAttribute' => ['marketplace_id', 'category_id'], 'message' => 'The combination of Marketplace ID and Category ID has already been taken.']
        ];
    }

    /**
     * Returns related Marketplace
     *
     * @return ActiveQuery
     */
    public function getMarketplace()
    {
        return $this->hasOne(Marketplace::class, ['id' => 'marketplace_id']);
    }

    /**
     * Returns related Category
     *
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }
}
