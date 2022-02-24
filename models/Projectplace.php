<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2022 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\models;

use humhub\modules\space\models\Space;
use humhub\modules\xcoin\utils\StringUtils;
use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;

/**
 * This is the model class for table "xcoin_projectplace".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $space_id
 * @property integer $invest_asset_id
 * @property integer $reward_asset_id
 * @property string $created_at
 * @property string $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 *
 * @property Space $space
 * @property Asset $investAsset
 * @property Asset $rewardAsset
 * @property User $createdBy
 * @property User $updatedBy
 */
class Projectplace extends ActiveRecord
{
    const SCENARIO_INSERT = 'insert';
    const SCENARIO_UPDATE = 'update';

    public $cover;

    public static function tableName()
    {
        return 'xcoin_projectplace';
    }

    public function rules()
    {
        return [
            [['title', 'description', 'cover'], 'required'],
            [['space_id', 'invest_asset_id', 'reward_asset_id'], 'integer'],
            ['space_id', 'exist', 'skipOnError' => true, 'targetClass' => Space::class, 'targetAttribute' => ['space_id' => 'id']],
            ['invest_asset_id', 'exist', 'skipOnError' => true, 'targetClass' => Asset::class, 'targetAttribute' => ['invest_asset_id' => 'id']],
            ['reward_asset_id', 'exist', 'skipOnError' => true, 'targetClass' => Asset::class, 'targetAttribute' => ['reward_asset_id' => 'id']],
            ['title', 'string', 'max' => 255],
            ['description', 'string'],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_INSERT => [
                'title',
                'description',
                'invest_asset_id',
                'reward_asset_id',
                'cover',
            ],
            self::SCENARIO_UPDATE => [
                'title',
                'description',
                'cover',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('XcoinModule.Projectplace', 'ID'),
            'title' => Yii::t('XcoinModule.Projectplace', 'Title'),
            'description' => Yii::t('XcoinModule.Projectplace', 'Description'),
            'invest_asset_id' => Yii::t('XcoinModule.Projectplace', 'Invest Coin'),
            'reward_asset_id' => Yii::t('XcoinModule.Projectplace', 'Reward Coin'),
            'cover' => Yii::t('XcoinModule.Projectplace', 'Cover'),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->fileManager->attach($this->cover);

        parent::afterSave($insert, $changedAttributes);
    }

    public function getSpace()
    {
        return $this->hasOne(Space::class, ['id' => 'space_id']);
    }

    public function getInvestAsset()
    {
        return $this->hasOne(Asset::class, ['id' => 'invest_asset_id']);
    }

    public function getRewardAsset()
    {
        return $this->hasOne(Asset::class, ['id' => 'reward_asset_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function shortenDescription()
    {
        return StringUtils::shorten($this->description, 100);
    }

    public function getCover()
    {
        $attachments = $this->fileManager->findAll();

        // return only the first element since a Projectplace can have only one cover attached
        return reset($attachments);
    }
}
