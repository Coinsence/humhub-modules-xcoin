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
 * This is the model class for table "xcoin_voucher".
 *
 * @property integer $id
 * @property string $value
 * @property integer $status
 * @property integer $product_id
 *
 * @property Product $product
 */
class Voucher extends ActiveRecord
{
    // Voucher status
    const STATUS_USED = 0;
    const STATUS_READY = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_voucher';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['value', 'required'],
            ['value', 'string', 'max' => 255],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    public static function findOneByValueAndProduct($value, $productId)
    {
         return Voucher::find()->where(['value' => $value, 'product_id' => $productId])->one();
    }

    public static function create($value, $productId)
    {
        $voucher = new self();

        $voucher->value = $value;
        $voucher->product_id = $productId;
        $voucher->status = self::STATUS_READY;

        return $voucher;
    }
}
