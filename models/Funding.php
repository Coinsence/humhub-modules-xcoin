<?php

namespace humhub\modules\xcoin\models;

use DateTime;
use humhub\components\ActiveRecord;
use humhub\libs\DbDateValidator;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AccountHelper;

use Yii;

/**
 * This is the model class for table "xcoin_funding".
 *
 * @property integer $id
 * @property integer $space_id
 * @property integer $asset_id
 * @property integer $exchange_rate
 * @property integer $total_amount
 * @property integer $available_amount
 * @property string $created_at
 * @property integer $created_by
 * @property string $title
 * @property string $description
 * @property string $deadline
 * @property string $content
 *
 * @property Asset $asset
 * @property User $createdBy
 * @property Space $space
 */
class Funding extends ActiveRecord
{

    const SCENARIO_EDIT = 'sedit';

    public $amount = 1;

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
            //  'total_amount', 'available_amount',
            [
                [
                    'space_id',
                    'asset_id',
                    'exchange_rate',
                    'created_by',
                    'available_amount',
                    'title',
                    'description',
                    'deadline',
                    'content'
                ], 'required'
            ],
            [['space_id', 'asset_id', 'total_amount', 'created_by'], 'integer'],
            [['available_amount'], 'number', 'min' => '0'],
            [['exchange_rate'], 'number', 'min' => '0.001'],
            [['created_at'], 'safe'],
            [['asset_id'], 'exist', 'skipOnError' => true, 'targetClass' => Asset::class, 'targetAttribute' => ['asset_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::class, 'targetAttribute' => ['space_id' => 'id']],
            [['title'], 'string', 'max' => 255],
            [['description', 'content'], 'string'],
            [['deadline'], DbDateValidator::class],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_EDIT => [
                'asset_id',
                'exchange_rate',
                'available_amount',
                'title',
                'description',
                'content',
                'deadline'
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
            'space_id' => 'Space ID',
            'asset_id' => 'Requested asset',
            'exchange_rate' => 'Exchange rate',
            'total_amount' => 'Total Amount',
            'available_amount' => 'Amount',
            'created_at' => 'Created At',
            'created_by' => 'Created By',
            'amount' => 'Offered asset',
            'amountConverted' => 'Wanted',
            'title' => 'Title',
            'description' => 'Description',
            'content' => 'Needs & Commitments',
            'deadline' => 'Deadline',
        ];
    }

    public function attributeHints()
    {
        return [
            'available_amount' => 'Maximum amount that can be exchanged with the asset at the specified rate. The available amount will also be automatically adjusted if the funds in the funding account are not sufficient. If set to 0 this exchange option is disabled.'
        ];
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
        return $this->total_amount / $this->exchange_rate;
    }

    /**
     * Gets the raised amount in the target asset
     *
     * @return float
     */

    public function getRaisedAmount()
    {
        return $this->getFundingAccount()->getAssetBalance($this->asset);
    }

    /**
     * Gets the raised amount in the target asset
     *
     * @return float
     */

    public function getRaisedPercentage()
    {
        if ($this->getRequestedAmount()) {
            return round(($this->getRaisedAmount() / $this->getRequestedAmount()) * 100);
        }

        return 0;
    }


    /**
     * Gets the offering amount percentage
     *
     * @return float
     */

    public function getOfferedAmountPercentage()
    {
        return round(($this->total_amount / Asset::findOne(['space_id' => $this->space_id])->getIssuedAmount()) * 100);
    }

    /**
     * Returns the available funding space assets based on funding account balance and available amount
     *
     * @return int
     */
    public function getBaseMaximumAmount()
    {
        // Funding Account Balance
        $balance = AccountHelper::getFundingAccountBalance($this->space);

        if ($balance < $this->available_amount) {
            return $balance;
        }

        return $this->available_amount;
    }

    public function getFundingAccount()
    {
        return AccountHelper::getFundingAccount($this->space);
    }

    public function isFirstStep()
    {
        return empty($this->asset_id);
    }

    public function isSecondStep()
    {
        return empty($this->available_amount) || empty($this->exchange_rate);
    }

    public function isThirdStep()
    {
        return empty($this->title) || empty($this->description) || empty($this->content) || empty($this->deadline);
    }

    public function canDelete()
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

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->total_amount = $this->available_amount;
        }

        return parent::beforeSave($insert);
    }

    public function shortenDescription()
    {
        return (strlen($this->description) > 100) ? substr($this->description, 0, 97) . '...' : $this->description;
    }

    public function canInvest()
    {
        return $this->getBaseMaximumAmount() == 0 && $this->getRemainingDays() > 0;
    }
}
