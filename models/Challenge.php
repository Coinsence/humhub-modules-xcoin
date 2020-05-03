<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
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
 * This is the model class for table "xcoin_challenge".
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
 *
 * @property Asset $asset
 * @property User $createdBy
 * @property Space $space
 */
class Challenge extends ActiveRecord
{

    const SCENARIO_CREATE = 'screate';
    const SCENARIO_EDIT = 'sedit';
    const SCENARIO_EDIT_ADMIN = 'seditadmin';

    // challenges statuses
    const CHALLENGE_STATUS_DISABLED = 0;
    const CHALLENGE_STATUS_ENABLED = 1;
    const CHALLENGE_ACTIVE = 0;
    const CHALLENGE_STOPPED = 1;

    public $coverFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_challenge';
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
            [['title'], 'string', 'max' => 255],
            [['description'], 'string'],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'asset_id',
                'title',
                'description'
            ],
            self::SCENARIO_EDIT => [
                'asset_id',
                'title',
                'description',
                'stopped'
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
            'id' => Yii::t('XcoinModule.challenge', 'ID'),
            'space_id' => Yii::t('XcoinModule.challenge', 'Space ID'),
            'asset_id' => Yii::t('XcoinModule.challenge', 'Requested coin'),
            'title' => Yii::t('XcoinModule.challenge', 'Title'),
            'description' => Yii::t('XcoinModule.challenge', 'Description'),
            'created_at' => Yii::t('XcoinModule.challenge', 'Created At'),
            'created_by' => Yii::t('XcoinModule.challenge', 'Created By')
        ];
    }


    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::CHALLENGE_STATUS_DISABLED;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        // TODO delete related projects
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
    public function getFundings()
    {
        return $this->hasMany(Funding::class, ['challenge_id' => 'id']);
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
            'object_model' => Challenge::class,
            'object_id' => $this->id
        ])->orderBy(['id' => SORT_DESC])->one();
    }

    public function isStopped()
    {
        return $this->stopped == 1;
    }
}
