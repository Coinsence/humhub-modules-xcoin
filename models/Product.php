<?php

namespace humhub\modules\xcoin\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use yii\db\ActiveQuery;
use yii\web\UploadedFile;

/**
 * This is the model class for table "xcoin_product".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property float $price
 * @property string $content
 * @property integer asset_id
 * @property integer created_by
 * @property string $created_at
 *
 * @property Asset $asset
 * @property User $owner
 */
class Product extends ActiveRecord
{
    const SCENARIO_EDIT = 'sedit';
    const SCENARIO_CREATE = 'screate';

    /**
     * @var UploadedFile
     */
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
            [['asset_id', 'created_by'], 'integer'],
            [['price'], 'number', 'min' => '0'],
            [['created_at'], 'safe'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::class, 'targetAttribute' => ['asset_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['name', 'description'], 'string', 'max' => 255],
            [['content'], 'string'],
//            [['pictureFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
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
                'pictureFile'
            ],
            self::SCENARIO_EDIT => [
                'name',
                'description',
                'price',
                'content',
                'asset_id',
                'pictureFile'
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
            'pictureFile' => 'Picture'
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
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function shortenDescription()
    {
        return (strlen($this->description) > 100) ? substr($this->description, 0, 97) . '...' : $this->description;
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->pictureFile->saveAs('uploads/' . $this->pictureFile->baseName . '.' . $this->pictureFile->extension);
            return true;
        } else {
            return false;
        }
    }
}
