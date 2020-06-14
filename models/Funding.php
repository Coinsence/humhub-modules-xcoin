<?php

namespace humhub\modules\xcoin\models;

use Colors\RandomColor;
use cornernote\linkall\LinkAllBehavior;
use DateTime;
use Exception;
use humhub\components\ActiveRecord;
use humhub\libs\DbDateValidator;
use humhub\modules\file\models\File;
use humhub\modules\space\components\UrlValidator;
use humhub\modules\space\Module;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\Utils;
use Yii;
use yii\db\ActiveQuery;
use yii\web\HttpException;

/**
 * This is the model class for table "xcoin_funding".
 *
 * @property integer $id
 * @property integer $space_id
 * @property integer $challenge_id
 * @property integer $exchange_rate
 * @property integer $amount
 * @property string $created_at
 * @property integer $created_by
 * @property string $title
 * @property string $description
 * @property string $deadline
 * @property string $content
 * @property integer $review_status
 * @property integer $status
 * @property string $country
 * @property string $city
 *
 * @property Challenge $challenge
 * @property User $createdBy
 * @property Space $space
 */
class Funding extends ActiveRecord
{
    const SCENARIO_NEW = 'snew';
    const SCENARIO_EDIT = 'sedit';

    // Funding review status
    const FUNDING_NOT_REVIEWED = 0;
    const FUNDING_REVIEWED = 1;

    // Funding status
    const FUNDING_STATUS_IN_PROGRESS = 0;
    const FUNDING_STATUS_INVESTMENT_ACCEPTED = 1;
    const FUNDING_STATUS_INVESTMENT_RESTARTED = 2;

    // used in readonly for setting up exchange rate
    public $rate = 1;

    // use when creating funding
    public $categories_names;

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
                    'challenge_id',
                    'created_by',
                    'amount',
                    'title',
                    'description',
                    'deadline',
                    'content',
                    'country',
                    'city',
                ],
                'required'
            ],
            ['categories_names', 'required', 'message' => 'Please choose at least a category'],
            [['space_id', 'challenge_id', 'amount', 'created_by'], 'integer'],
            [['amount'], 'number', 'min' => '1'],
            [['exchange_rate'], 'number', 'min' => '0.1'],
            [['created_at'], 'safe'],
            [['challenge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Challenge::class, 'targetAttribute' => ['challenge_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::class, 'targetAttribute' => ['space_id' => 'id']],
            [['title', 'description', 'city'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 2],
            [['content'], 'string'],
            [['deadline'], DbDateValidator::class],
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
            self::SCENARIO_NEW => [
                'challenge_id',
                'amount',
                'title',
                'description',
                'content',
                'deadline',
                'space_id',
                'exchange_rate',
                'country',
                'city',
                'categories_names'
            ],
            self::SCENARIO_EDIT => [
                'amount',
                'title',
                'description',
                'content',
                'deadline',
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
            'space_id' => Yii::t('XcoinModule.base', 'Space ID'),
            'challenge_id' => Yii::t('XcoinModule.base', 'Challenge'),
            'exchange_rate' => Yii::t('XcoinModule.base', 'Exchange rate'),
            'amount' => Yii::t('XcoinModule.base', 'Requested Amount'),
            'created_at' => Yii::t('XcoinModule.base', 'Created At'),
            'created_by' => Yii::t('XcoinModule.base', 'Created By'),
            'title' => Yii::t('XcoinModule.funding', 'Title'),
            'description' => Yii::t('XcoinModule.base', 'Description'),
            'content' => Yii::t('XcoinModule.base', 'Needs & Commitments'),
            'deadline' => Yii::t('XcoinModule.base', 'Deadline'),
            'country' => Yii::t('XcoinModule.base', 'Country'),
            'city' => Yii::t('XcoinModule.base', 'City'),
        ];
    }

    public function attributeHints()
    {
        return [
            'exchange_rate' => Yii::t('XcoinModule.base', 'How many space coins are you offering per requested coin.')
        ];
    }

    public function init()
    {
        if ($this->isNewRecord) {
            $this->exchange_rate = 1;
        }
        parent::init();
    }


    /**
     * @inheritdoc
     *
     * @throws HttpException
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->status = self::FUNDING_STATUS_IN_PROGRESS;

            //create funding account
            AccountHelper::getFundingAccount($this);

            if (!$this->space_id) {
                $this->AttachSpace();
            }
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert)
            $this->createFundingAccount();
        else {
            if (isset($changedAttributes['amount']) && $changedAttributes['amount'] != $this->amount)
                $this->adjustIssuesAmount();
        }

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
     * @inheritdoc
     */
    public function beforeDelete()
    {
        $fundingAccount = $this->getFundingAccount();

        $fundingAccount->revertTransactions();

        return parent::beforeDelete();
    }

    /**
     * @return ActiveQuery
     */
    public function getChallenge()
    {
        return $this->hasOne(Challenge::class, ['id' => 'challenge_id']);
    }

    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('xcoin_funding_category', ['funding_id' => 'id']);
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

    /**
     * Gets the requested amount in the target asset
     *
     * @return float
     */

    public function getRequestedAmount()
    {
        return $this->amount;
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
        return empty($this->challenge_id);
    }

    public function isSecondStep()
    {
        return Utils::mempty(
                $this->amount,
                $this->exchange_rate,
                $this->title,
                $this->description,
                $this->content,
                $this->deadline,
                $this->country,
                $this->city
            ) || strlen($this->description) > 255;
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
     * @throws Exception
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
        if (Space::findOne(['name' => $this->title])) {
            $this->addError('title', Yii::t('XcoinModule.funding', 'Title already used'));

            return false;
        }

        return true;
    }

    public function getCover()
    {
        return File::find()->where([
            'object_model' => Funding::class,
            'object_id' => $this->id,
            'show_in_stream' => true
        ])->orderBy(['id' => SORT_ASC])->one();
    }

    public function getGallery()
    {
        $gallery = File::find()->where([
            'object_model' => Funding::class,
            'object_id' => $this->id,
            'show_in_stream' => true
        ])->orderBy(['id' => SORT_ASC])->all();

        //removing cover
        array_shift($gallery);

        return $gallery;
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
        $space->name = $this->title;
        $space->description = $this->description;
        $space->url = UrlValidator::autogenerateUniqueSpaceUrl($this->title);

        if (!$space->save()) {
            throw new HttpException(400);
        }

        $this->space_id = $space->id;
    }

    private function createFundingAccount()
    {
        $account = new Account();
        $account->space_id = $this->space->id;
        $account->title = "Campaign # $this->id";
        $account->account_type = Account::TYPE_FUNDING;
        $account->funding_id = $this->id;

        if (!$account->save()) {
            throw new \yii\base\Exception('Could not create funding account!');
        }

        $asset = AssetHelper::getSpaceAsset($this->space);
        if ($asset === null) {
            throw new HttpException(404);
        }

        $issueAccount = AccountHelper::getIssueAccount($this->space);

        $transaction = new Transaction();
        $transaction->transaction_type = Transaction::TRANSACTION_TYPE_ISSUE;
        $transaction->asset_id = $asset->id;
        $transaction->from_account_id = $issueAccount->id;
        $transaction->to_account_id = $account->id;
        $transaction->amount = $this->amount * $this->exchange_rate;

        if (!$transaction->save()) {
            throw new Exception('Could not create issue transaction for funding account');
        }
    }

    private function adjustIssuesAmount()
    {
        if (!$fundingAccount = Account::findOne(['funding_id' => $this->id]))
            throw new HttpException(404);

        if (!$issueAccount = AccountHelper::getIssueAccount($this->space))
            throw new HttpException(404);

        if (!$asset = AssetHelper::getSpaceAsset($this->space))
            throw new HttpException(404);

        $transaction = new Transaction();
        $transaction->asset_id = $asset->id;

        if ($this->amount > $fundingAccount->getAssetBalance($asset)) {
            $transaction->transaction_type = Transaction::TRANSACTION_TYPE_ISSUE;
            $transaction->from_account_id = $issueAccount->id;
            $transaction->to_account_id = $fundingAccount->id;
            $transaction->amount = ($this->amount - $fundingAccount->getAssetBalance($asset)) * $this->exchange_rate;
        } else {
            $transaction->transaction_type = Transaction::TRANSACTION_TYPE_REVERT;
            $transaction->from_account_id = $fundingAccount->id;
            $transaction->to_account_id = $issueAccount->id;
            $transaction->amount = ($fundingAccount->getAssetBalance($asset) - $this->amount) * $this->exchange_rate;
        }

        if (!$transaction->save()) {
            throw new Exception('Could not create issue transaction for funding account');
        }
    }
}
