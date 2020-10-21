<?php

namespace humhub\modules\xcoin\models;

use Colors\RandomColor;
use cornernote\linkall\LinkAllBehavior;
use humhub\components\ActiveRecord;
use humhub\modules\file\models\File;
use humhub\modules\space\components\UrlValidator;
use humhub\modules\space\models\Space;
use humhub\modules\space\Module;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\helpers\Utils;
use Yii;
use yii\db\ActiveQuery;
use yii\web\HttpException;

/**
 * This is the model class for table "xcoin_product".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property string $content
 * @property integer $marketplace_id
 * @property integer $created_by
 * @property string $created_at
 * @property integer $space_id
 * @property integer $product_type
 * @property integer $offer_type
 * @property integer $status
 * @property float $discount
 * @property integer $payment_type
 * @property integer $review_status
 * @property string $country
 * @property string $city
 *
 * @property Marketplace $marketplace
 * @property User $owner
 * @property Space $space
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

    // used when creating product
    public $categories_names;

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
            [['name', 'description', 'content', 'marketplace_id', 'offer_type', 'city', 'country'], 'required'],
            ['categories_names', 'required', 'message' => 'Please choose at least a category'],
            [['price', 'payment_type'], 'required', 'when' => function ($model) {
                return $model->offer_type == Product::OFFER_TOTAL_PRICE_IN_COINS;
            }],
            [['discount'], 'required', 'when' => function ($model) {
                return $model->offer_type == Product::OFFER_DISCOUNT_FOR_COINS;
            }],
            [['marketplace_id', 'created_by', 'product_type', 'space_id', 'sale_type', 'status', 'offer_type', 'payment_type'], 'integer'],
            [['price'], 'number', 'min' => '0'],
            [['discount'], 'number', 'min' => '0', 'max' => '100'],
            [['created_at'], 'safe'],
            [['marketplace_id'], 'exist', 'skipOnError' => true, 'targetClass' => Marketplace::class, 'targetAttribute' => ['marketplace_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::class, 'targetAttribute' => ['space_id' => 'id']],
            [['name', 'description'], 'string', 'max' => 255],
            [['content'], 'string'],
            [['pictureFile'], 'safe'],
        ];
    }

    public function behaviors()
    {
        return [
            LinkAllBehavior::class,
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'name',
                'space_id',
                'description',
                'price',
                'content',
                'marketplace_id',
                'offer_type',
                'discount',
                'payment_type',
                'categories_names',
                'country',
                'city'
            ],
            self::SCENARIO_EDIT => [
                'name',
                'description',
                'price',
                'content',
                'offer_type',
                'status',
                'discount',
                'payment_type',
                'country',
                'city'
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
            'marketplace_id' => Yii::t('XcoinModule.base', 'Marketplace'),
            'price' => Yii::t('XcoinModule.base', 'Price'),
            'created_at' => Yii::t('XcoinModule.base', 'Created At'),
            'created_by' => Yii::t('XcoinModule.base', 'Created By'),
            'name' => Yii::t('XcoinModule.base', 'Name'),
            'description' => Yii::t('XcoinModule.base', 'Description'),
            'content' => Yii::t('XcoinModule.base', 'Detailed Description'),
            'offer_type' => Yii::t('XcoinModule.base', 'Offer Type'),
            'status' => Yii::t('XcoinModule.base', 'Status'),
            'discount' => Yii::t('XcoinModule.base', 'Discount in %'),
            'payment_type' => Yii::t('XcoinModule.base', 'Payment Type'),
            'country' => Yii::t('XcoinModule.base', 'Country'),
            'city' => Yii::t('XcoinModule.base', 'City'),
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::STATUS_AVAILABLE;

            if (!$this->space_id) {
                $this->AttachSpace();
            }
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->categories_names) {
            $categories = [];

            foreach (explode(",", $this->categories_names) as $category_name) {
                $category = Category::getCategoryByName($category_name);
                if ($category) {
                    $categories[] = $category;
                }
            }
            $this->linkAll('categories', $categories);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @return ActiveQuery
     */
    public function getMarketplace()
    {
        return $this->hasOne(Marketplace::class, ['id' => 'marketplace_id']);
    }

    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('xcoin_product_category', ['product_id' => 'id']);
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
        return File::find()->where([
            'object_model' => Product::class,
            'object_id' => $this->id,
            'show_in_stream' => true
        ])->orderBy(['id' => SORT_ASC])->one();
    }

    public function getGallery()
    {
        $gallery = File::find()->where([
            'object_model' => Product::class,
            'object_id' => $this->id,
            'show_in_stream' => true
        ])->orderBy(['id' => SORT_ASC])->all();

        //removing cover
        array_shift($gallery);

        return $gallery;
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

    public function isNameUnique()
    {
        if (Space::findOne(['name' => $this->name])) {
            $this->addError('name', Yii::t('XcoinModule.product', 'Name already used'));

            return false;
        }

        return true;
    }

    public function isFirstStep()
    {
        return empty($this->marketplace_id);
    }

    public function isSecondStep()
    {
        return Utils::mempty(
                $this->name,
                $this->description,
                $this->content,
                $this->country,
                $this->city,
                $this->offer_type
            ) || strlen($this->description) > 255 ||
            ($this->offer_type == self::OFFER_DISCOUNT_FOR_COINS && empty($this->discount)) ||
            ($this->offer_type == self::OFFER_TOTAL_PRICE_IN_COINS && (empty($this->price) || empty($this->payment_type)));
    }

    private function AttachSpace()
    {
        /* @var Module $module */
        $module = Yii::$app->getModule('space');

        // init space model
        $space = new Space();
        $space->scenario = Space::SCENARIO_CREATE;
        $space->visibility = $module->settings->get('defaultVisibility', Space::VISIBILITY_REGISTERED_ONLY);
        $space->join_policy = $module->settings->get('defaultJoinPolicy', Space::JOIN_POLICY_APPLICATION);
        $space->color = RandomColor::one(['luminosity' => 'dark']);
        $space->space_type = Space::SPACE_TYPE_FUNDING;
        $space->name = $this->name;
        $space->description = $this->description;
        $space->url = UrlValidator::autogenerateUniqueSpaceUrl($this->name);

        if (!$space->save()) {
            throw new HttpException(400);
        }

        $this->space_id = $space->id;
    }
}
