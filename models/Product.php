<?php

namespace humhub\modules\xcoin\models;

use humhub\components\ActiveRecord;
use humhub\modules\file\models\File;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "xcoin_product".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property string $content
 * @property integer $asset_id
 * @property integer $created_by
 * @property string $created_at
 * @property integer $space_id
 * @property integer $product_type
 * @property integer $payment_type
 * @property integer $status
 *
 * @property Asset $asset
 * @property User $owner
 */
class Product extends ActiveRecord
{
    // Model scenarios
    const SCENARIO_EDIT = 'sedit';
    const SCENARIO_CREATE = 'screate';

    // Product type
    const TYPE_PERSONAL = 1;
    const TYPE_SPACE = 2;

    // Product payment type
    const PAYMENT_PER_UNIT = 1;
    const PAYMENT_PER_HOUR = 2;
    const PAYMENT_PER_DAY = 3;

    // Product status
    const STATUS_UNAVAILABLE = 0;
    const STATUS_AVAILABLE = 1;

    public $pictureFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'description', 'price', 'content', 'asset_id'], 'required'],
            [['asset_id', 'created_by', 'product_type', 'space_id', 'sale_type', 'status',], 'integer'],
            [['price'], 'number', 'min' => '0'],
            [['created_at'], 'safe'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::class, 'targetAttribute' => ['asset_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['name', 'description'], 'string', 'max' => 255],
            [['content'], 'string'],
            [['pictureFile'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'name',
                'description',
                'price',
                'content',
                'asset_id',
                'payment_type',
            ],
            self::SCENARIO_EDIT => [
                'name',
                'description',
                'price',
                'content',
                'asset_id',
                'payment_type',
                'status'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'asset_id' => 'Payment asset',
            'price' => 'Price',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'name' => 'Name',
            'description' => 'Description',
            'content' => 'Detailed Description',
            'payment_type' => 'Payment Type',
            'status' => 'Status',
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(Asset::class, ['id' => 'asset_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSpace()
    {
        return $this->hasOne(Space::class, ['id' => 'space_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function shortenDescription()
    {
        return (strlen($this->description) > 100) ? substr($this->description, 0, 97) . '...' : $this->description;
    }

    public function getPicture()
    {
        $cover = File::find()->where([
            'object_model' => Product::class,
            'object_id' => $this->id
        ])->orderBy(['id' => SORT_ASC])->one();

        return $cover;
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_AVAILABLE;
        }

        return parent::beforeSave($insert);
    }

    public function getPaymentTypes()
    {
        return [
            self::PAYMENT_PER_UNIT => 'Per Unit',
            self::PAYMENT_PER_HOUR => 'Per Hour',
            self::PAYMENT_PER_DAY => 'Per Day'
        ];
    }

    public function getPaymentType()
    {
        return $this->getPaymentTypes()[$this->payment_type];
    }

    public function isSpaceProduct()
    {
        return $this->product_type == self::TYPE_SPACE;
    }
}
