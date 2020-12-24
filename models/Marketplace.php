<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\models;

use Yii;
use yii\db\ActiveQuery;
use humhub\components\ActiveRecord;
use humhub\modules\file\models\File;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;

/**
 * This is the model class for table "xcoin_marketplace".
 *
 * @property integer $id
 * @property integer $space_id
 * @property integer $asset_id
 * @property string $title
 * @property string $description
 * @property string $created_at
 * @property integer $created_by
 * @property integer $status
 * @property integer $stopped
 * @property string $action_name
 * @property integer $is_link_required
 *
 * @property Asset $asset
 * @property User $createdBy
 * @property Space $space
 */
class Marketplace extends ActiveRecord
{

    const SCENARIO_CREATE = 'screate';
    const SCENARIO_EDIT = 'sedit';
    const SCENARIO_EDIT_ADMIN = 'seditadmin';

    // marketplace statuses
    const MARKETPLACE_STATUS_DISABLED = 0;
    const MARKETPLACE_STATUS_ENABLED = 1;
    const MARKETPLACE_ACTIVE = 0;
    const MARKETPLACE_STOPPED = 1;

    public $coverFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_marketplace';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['space_id', 'asset_id', 'title', 'description', 'created_by'], 'required'],
            [['space_id', 'asset_id', 'created_by'], 'integer'],
            [['created_at', 'status', 'stopped'], 'safe'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::class, 'targetAttribute' => ['asset_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::class, 'targetAttribute' => ['space_id' => 'id']],
            [['title', 'action_name'], 'string', 'max' => 255],
            [['description'], 'string'],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'asset_id',
                'title',
                'description',
                'action_name',
                'is_link_required'
            ],
            self::SCENARIO_EDIT => [
                'asset_id',
                'title',
                'description',
                'stopped',
                'action_name',
                'is_link_required'
            ],
            self::SCENARIO_EDIT_ADMIN => [
                'status',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('XcoinModule.marketplace', 'ID'),
            'space_id' => Yii::t('XcoinModule.marketplace', 'Space ID'),
            'asset_id' => Yii::t('XcoinModule.marketplace', 'Requested coin'),
            'title' => Yii::t('XcoinModule.marketplace', 'Title'),
            'description' => Yii::t('XcoinModule.marketplace', 'Description'),
            'created_at' => Yii::t('XcoinModule.marketplace', 'Created At'),
            'created_by' => Yii::t('XcoinModule.marketplace', 'Created By'),
            'action_name' => Yii::t('XcoinModule.marketplace', 'Call to action'),
            'is_link_required' => Yii::t('XcoinModule.marketplace', 'Product call to action link'),
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::MARKETPLACE_STATUS_DISABLED;
        }

        if (!$this->action_name) {
            $this->action_name = Yii::t('XcoinModule.marketplace', 'Buy Product');
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        // TODO delete related products
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
    public function getProducts()
    {
        return $this->hasMany(Product::class, ['marketplace_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return ActiveQuery
     */
    public function getSpace()
    {
        return $this->hasOne(Space::class, ['id' => 'space_id']);
    }

    public function canDeleteFile()
    {
        $space = Space::findOne(['id' => $this->space_id]);

        if ($space->isAdmin(Yii::$app->user->identity))
            return true;

        return false;
    }

    public function shortenDescription()
    {
        return (strlen($this->description) > 100) ? substr($this->description, 0, 97) . '...' : $this->description;
    }

    public function getCover()
    {
        return File::find()->where([
            'object_model' => Marketplace::class,
            'object_id' => $this->id,
            'show_in_stream' => true
        ])->orderBy(['id' => SORT_DESC])->one();
    }

    public function isStopped()
    {
        return $this->stopped == self::MARKETPLACE_STOPPED;
    }

    public function isDisabled()
    {
        return $this->status == self::MARKETPLACE_STATUS_DISABLED;
    }

    public function isLinkRequired()
    {
        return $this->is_link_required == 1;
    }
}
