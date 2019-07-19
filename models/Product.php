<?php

namespace humhub\modules\xcoin\models;

use humhub\components\ActiveRecord;
use humhub\modules\file\models\File;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;
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
 * @property integer $offer_type
 * @property integer $status
 * @property float $discount
 * @property integer $payment_type
 * @property integer $review_status
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

    // Product offer type
    const OFFER_DISCOUNT_FOR_COINS = 1;
    const OFFER_TOTAL_PRICE_IN_COINS = 2;

    // Product status
    const STATUS_UNAVAILABLE = 0;
    const STATUS_AVAILABLE = 1;

    // Product payment types
    const PAYMENT_PER_UNIT = 1;
    const PAYMENT_PER_HOUR = 2;
    const PAYMENT_PER_DAY = 3;
    const PAYMENT_PER_SERVICE = 4;

    // Product review status
    const PRODUCT_NOT_REVIEWED = 0;
    const PRODUCT_REVIEWED = 1;

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
            [['name', 'description', 'content', 'asset_id', 'offer_type'], 'required'],
            [['price', 'payment_type'], 'required', 'when' => function ($model) {
                return $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS;
            }],
            [['discount'], 'required', 'when' => function ($model) {
                return $model->offer_type == Product::OFFER_DISCOUNT_FOR_COINS;
            }],
            [['asset_id', 'created_by', 'product_type', 'space_id', 'sale_type', 'status', 'offer_type', 'payment_type'], 'integer'],
            [['price'], 'number', 'min' => '0'],
            [['discount'], 'number', 'min' => '0', 'max' => '100'],
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
                'offer_type',
                'discount',
                'payment_type'
            ],
            self::SCENARIO_EDIT => [
                'name',
                'description',
                'price',
                'content',
                'asset_id',
                'offer_type',
                'status',
                'discount',
                'payment_type'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('XcoinModule.base', 'ID'),
            'asset_id' => Yii::t('XcoinModule.base', 'Requested Coin'),
            'price' => Yii::t('XcoinModule.base', 'Price'),
            'created_at' => Yii::t('XcoinModule.base', 'Created At'),
            'created_by' => Yii::t('XcoinModule.base', 'Created By'),
            'name' => Yii::t('XcoinModule.base', 'Name'),
            'description' => Yii::t('XcoinModule.base', 'Description'),
            'content' => Yii::t('XcoinModule.base', 'Detailed Description'),
            'offer_type' => Yii::t('XcoinModule.base', 'Offer Type'),
            'status' => Yii::t('XcoinModule.base', 'Status'),
            'discount' => Yii::t('XcoinModule.base', 'Discount in %'),
            'payment_type' => Yii::t('XcoinModule.base', 'Payment Type')
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

    public static function getOfferTypes()
    {
        return [
            self::OFFER_DISCOUNT_FOR_COINS => Yii::t('XcoinModule.base', 'Discount for coins'),
            self::OFFER_TOTAL_PRICE_IN_COINS => Yii::t('XcoinModule.base', 'Total price in coins'),
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_AVAILABLE => Yii::t('XcoinModule.base', 'Available'),
            self::STATUS_UNAVAILABLE => Yii::t('XcoinModule.base', 'Unavailable'),
        ];
    }

    public static function getPaymentTypes()
    {
        return [
            self::PAYMENT_PER_UNIT => Yii::t('XcoinModule.base', 'Per Unit'),
            self::PAYMENT_PER_HOUR => Yii::t('XcoinModule.base', 'Per Hour'),
            self::PAYMENT_PER_DAY => Yii::t('XcoinModule.base', 'Per Day'),
            self::PAYMENT_PER_SERVICE => Yii::t('XcoinModule.base', 'Per Service')
        ];
    }

    public function getPaymentType()
    {
        return static::getPaymentTypes()[$this->payment_type];
    }

    public function getOfferType()
    {
        return static::getOfferTypes()[$this->offer_type];
    }

    public function isSpaceProduct()
    {
        return $this->product_type == self::TYPE_SPACE;
    }

    public function canDeleteFile()
    {
        $space = Space::findOne(['id' => $this->space_id]);
        $actionPerformer = Yii::$app->user->identity;

        if (($space && $space->isAdmin($actionPerformer)) || $this->isOwner($actionPerformer)) {
            return true;
        }

        return false;
    }

    public function isOwner($user)
    {
        return $user->id == $this->getCreatedBy()->one()->id;
    }
}
