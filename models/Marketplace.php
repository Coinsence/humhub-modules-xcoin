<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\models;

use cornernote\linkall\LinkAllBehavior;
use humhub\modules\xcoin\utils\ImageUtils;
use Yii;
use yii\db\ActiveQuery;
use humhub\components\ActiveRecord;
use humhub\modules\file\models\File;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use yii\helpers\Url;

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
 * @property integer $hide_unverified_submissions
 * @property integer $status
 * @property integer $stopped
 * @property string $action_name
 * @property integer $selling_option
 * @property integer $is_tasks_marketplace
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

    // options
    const OPTION_SEND_MESSAGE = 0;
    const OPTION_REDIRECT_TO_LINK = 1;

    const TASK_MARKETPLACE_ACTIVE = 1;

    // unreviewed submissions options

    const UNREVIEWED_SUBMISSIONS_VISIBLE = 0;
    const UNREVIEWED_SUBMISSIONS_UNVISIBLE = 1;

    public $coverFile;

    // used when creating marketplace
    public $categories_names;

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
            ['categories_names', 'required', 'message' => 'Please choose at least a category'],
            [['space_id', 'asset_id', 'created_by', 'hide_unverified_submissions'], 'integer'],
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
                'selling_option',
                'categories_names',
                'is_tasks_marketplace',
                'hide_unverified_submissions'
            ],
            self::SCENARIO_EDIT => [
                'asset_id',
                'title',
                'description',
                'stopped',
                'action_name',
                'selling_option',
                'is_tasks_marketplace',
                'hide_unverified_submissions'
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
            'selling_option' => Yii::t('XcoinModule.marketplace', 'Options'),
            'is_tasks_marketplace' => Yii::t('XcoinModule.marketplace', 'Tasks Marketplace'),
            'hide_unverified_submissions' => Yii::t('XcoinModule.marketplace', 'Hide unverified Submissions'),
        ];
    }

    public function behaviors()
    {
        return [
            LinkAllBehavior::class,
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

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->categories_names) {
            $categories = [];

            $x = $this->categories_names;
            foreach ($this->categories_names as $category_name) {
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

    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('xcoin_marketplace_category', ['marketplace_id' => 'id']);
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

    public function getCroppedCover($type, $width, $height)
    {
        $file = $this->getCover();
        
        if (!$file) {
            return null;
        }

        $targetFile = $file->getStoredFilePath();
        $path = $targetFile . "file";
        $targetPath = ImageUtils::resizeImage($path, "product_image", $width, $height, $file->guid . "_".$type);

        return Url::base() . "/uploads/product_image/" . basename($targetPath);

    }

    public function isStopped()
    {
        return $this->stopped == self::MARKETPLACE_STOPPED;
    }

    public function isDisabled()
    {
        return $this->status == self::MARKETPLACE_STATUS_DISABLED;
    }

    public function shouldRedirectToLink()
    {
        return $this->selling_option == self::OPTION_REDIRECT_TO_LINK;
    }

    public function isTasksMarketplace()
    {
        return $this->is_tasks_marketplace == self::TASK_MARKETPLACE_ACTIVE;
    }

    public static function getOptions()
    {
        return [
            self::OPTION_SEND_MESSAGE => Yii::t('XcoinModule.base', 'Send a message to buyer'),
            self::OPTION_REDIRECT_TO_LINK => Yii::t('XcoinModule.base', 'Redirect to a link'),
        ];
    }

    public function hideUnreviewedSubmissions()
    {
        return $this->hide_unverified_submissions == self::UNREVIEWED_SUBMISSIONS_UNVISIBLE;
    }

    public function showUnreviewedSubmissions()
    {
        return $this->hide_unverified_submissions == self::UNREVIEWED_SUBMISSIONS_VISIBLE;
    }
}
