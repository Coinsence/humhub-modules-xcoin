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
            [['price'], 'required', 'when' => function ($model) {
                return $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS;
            }],
            [['discount'], 'required', 'when' => function ($model) {
                return $model->offer_type == Product::OFFER_DISCOUNT_FOR_COINS;
            }],
            [['asset_id', 'created_by', 'product_type', 'space_id', 'sale_type', 'status', 'offer_type'], 'integer'],
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
            'asset_id' => 'Requested Coin',
            'price' => 'Price',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'name' => 'Name',
            'description' => 'Description',
            'content' => 'Detailed Description',
            'offer_type' => 'Offer Type',
            'status' => 'Status',
            'discount' => 'Discount in %',
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
            self::OFFER_DISCOUNT_FOR_COINS => 'Discount for coins',
            self::OFFER_TOTAL_PRICE_IN_COINS => 'Total price in coins',
        ];
    }

    public static function getStatuses()
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_UNAVAILABLE => 'Unavailable',
        ];
    }

    public function getOfferType()
    {
        return $this->getOfferTypes()[$this->offer_type];
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
