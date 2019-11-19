<?php

namespace humhub\modules\xcoin\models;

use DateTime;
use humhub\components\ActiveRecord;
use humhub\libs\DbDateValidator;
use humhub\modules\file\models\File;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AccountHelper;

use humhub\modules\xcoin\helpers\AssetHelper;
use Yii;

/**
 * This is the model class for table "xcoin_funding".
 *
 * @property integer $id
 * @property integer $space_id
 * @property integer $asset_id
 * @property integer $exchange_rate
 * @property integer $amount
 * @property string $created_at
 * @property integer $created_by
 * @property string $title
 * @property string $description
 * @property string $deadline
 * @property string $content
 * @property integer $review_status
 *
 * @property Asset $asset
 * @property User $createdBy
 * @property Space $space
 */
class Funding extends ActiveRecord
{

    const SCENARIO_EDIT = 'sedit';

    // Funding review status
    const FUNDING_NOT_REVIEWED = 0;
    const FUNDING_REVIEWED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_funding';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'space_id',
                    'asset_id',
                    'created_by',
                    'amount',
                    'title',
                    'description',
                    'deadline',
                    'content'
                ],
                'required'
            ],
            [['space_id', 'asset_id', 'amount', 'created_by'], 'integer'],
            [['amount'], 'number', 'min' => '0'],
            [['created_at'], 'safe'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::class, 'targetAttribute' => ['asset_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::class, 'targetAttribute' => ['space_id' => 'id']],
            [['title', 'description'], 'string', 'max' => 255],
            [['content'], 'string'],
            [['deadline'], DbDateValidator::class],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_EDIT => [
                'asset_id',
                'amount',
                'title',
                'description',
                'content',
                'deadline',
                'space_id'
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
            'space_id' => Yii::t('XcoinModule.base', 'Space ID'),
            'asset_id' => Yii::t('XcoinModule.base', 'Requested asset'),
            'exchange_rate' => Yii::t('XcoinModule.base', 'Exchange rate'),
            'amount' => Yii::t('XcoinModule.base', 'Amount'),
            'created_at' => Yii::t('XcoinModule.base', 'Created At'),
            'created_by' => Yii::t('XcoinModule.base', 'Created By'),
            'title' => Yii::t('XcoinModule.funding', 'Title'),
            'description' => Yii::t('XcoinModule.base', 'Description'),
            'content' => Yii::t('XcoinModule.base', 'Needs & Commitments'),
            'deadline' => Yii::t('XcoinModule.base', 'Deadline'),
        ];
    }

    public function attributeHints()
    {
        return [
            'amount' => Yii::t('XcoinModule.base', 'If set to 0 this exchange option is disabled.')
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if($this->isNewRecord){
            $this->exchange_rate = 1;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        $fundingAccount = $this->getFundingAccount();

        foreach (Transaction::findAll(['to_account_id' => $fundingAccount->id]) as $transaction){
            $transaction->delete();
        }

        $fundingAccount->delete();

        return parent::beforeDelete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsset()
    {
        return $this->hasOne(Asset::class, ['id' => 'asset_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSpace()
    {
        return $this->hasOne(Space::class, ['id' => 'space_id']);
    }

    /**
     * Gets the requested amount in the target asset
     *
     * @return float
     */

    public function getRequestedAmount()
    {
        return round($this->amount / $this->exchange_rate);
    }

    /**
     * Gets the raised amount in the target asset
     *
     * @return float
     */
    public function getRaisedAmount()
    {
        return AccountHelper::getFundingAccountBalance($this);
    }

    /**
     * Gets the raised amount in the target asset
     *
     * @return float
     */

    public function getRaisedPercentage()
    {
        return $this->getRequestedAmount() ? round(($this->getRaisedAmount() / $this->getRequestedAmount()) * 100) : 0;
    }


    /**
     * Gets the offering amount percentage
     *
     * @return float
     */

    public function getOfferedAmountPercentage()
    {
        if (AssetHelper::getSpaceAsset($this->space)->getIssuedAmount()) {
            return round(($this->amount / AssetHelper::getSpaceAsset($this->space)->getIssuedAmount()) * 100);
        }

        return 100;
    }

    /**
     * Gets the available amount in the space asset
     *
     * @return float
     */
    public function getAvailableAmount()
    {
        return AccountHelper::getFundingAccountBalance($this, false);
    }

    public function getFundingAccount()
    {
        return AccountHelper::getFundingAccount($this);
    }

    public function isFirstStep()
    {
        return empty($this->asset_id) || empty($this->amount);
    }

    public function isSecondStep()
    {
        return empty($this->title) || empty($this->description) || empty($this->content) || empty($this->deadline) || strlen($this->description) > 255;
    }

    public function canDeleteFile()
    {
        $space = Space::findOne(['id' => $this->space_id]);

        if ($space->isAdmin(Yii::$app->user->identity))
            return true;

        return false;
    }

    /**
     * Calculate remaining days to deadline , current day is not omitted
     *
     * @return int
     * @throws \Exception
     */
    public function getRemainingDays()
    {
        $now = new DateTime();
        $deadline = (new DateTime($this->deadline))->modify('+1 day');
        $remainingDays = $deadline->diff($now)->format("%a");

        return intval($remainingDays);
    }

    public function shortenDescription()
    {
        return (strlen($this->description) > 100) ? substr($this->description, 0, 97) . '...' : $this->description;
    }

    public function canInvest()
    {
        return $this->getAvailableAmount() > 0 && $this->getRemainingDays() > 0;
    }

    public function isNameUnique()
    {
        if ($this->space->space_type == Space::SPACE_TYPE_FUNDING &&
            Space::findOne(['name' => $this->title])) {
            $this->addError('title' , Yii::t('XcoinModule.funding', 'Title already used'));
            return false;
        }

        return true;
    }

    public function getCover()
    {
        $cover = File::find()->where([
            'object_model' => Funding::class,
            'object_id' => $this->id
        ])->orderBy(['id' => SORT_ASC])->one();

        return $cover;
    }

    public function getGallery()
    {
        $gallery = File::find()->where([
            'object_model' => Funding::class,
            'object_id' => $this->id
        ])->orderBy(['id' => SORT_ASC])->all();

        //removing cover
        array_shift($gallery);

        return $gallery;
    }
}
