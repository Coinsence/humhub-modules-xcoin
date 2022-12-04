<?php

namespace humhub\modules\xcoin\models;

use Colors\RandomColor;
use cornernote\linkall\LinkAllBehavior;
use DateTime;
use Exception;
use humhub\components\ActiveRecord;
use humhub\components\Event;
use humhub\libs\DbDateValidator;
use humhub\modules\file\models\File;
use humhub\modules\space\components\UrlValidator;
use humhub\modules\user\models\User;
use humhub\modules\space\models\Space;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\FundingHelper;
use humhub\modules\xcoin\helpers\Utils;
use Symfony\Component\Filesystem\Filesystem;
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
 * @property integer $published
 * @property integer $activate_funding
 * @property string $country
 * @property string $city
 * @property string $youtube_link
 * @property integer $hidden_location
 * @property integer $hidden_details
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
    const FUNDING_LAUNCHING_SOON = 2;


    // Funding status
    const FUNDING_STATUS_IN_PROGRESS = 0;
    const FUNDING_STATUS_INVESTMENT_ACCEPTED = 1;
    const FUNDING_STATUS_INVESTMENT_RESTARTED = 2;

    // Deactivate or activate funding
    const FUNDING_DEACTIVATED = 0;
    const FUNDING_ACTIVATED = 1;

    // Hide or publish funding
    const FUNDING_HIDDEN = 0;
    const FUNDING_PUBLISHED = 1;

    // Hide or show location
    const FUNDING_LOCATION_SHOWN = 0;
    const FUNDING_LOCATION_HIDDEN = 1;

    // Hide or show details
    const FUNDING_DETAILS_SHOWN = 0;
    const FUNDING_DETAILS_HIDDEN = 1;

    // used in readonly for setting up exchange rate
    public $rate = 1;

    // used when creating funding
    public $categories_names;

    // used to select if this is a cloned product
    public $clone_id;

    // used to store temporarily the cloned image guid
    public $picture_file_guid;

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
            [['space_id', 'challenge_id', 'clone_id', 'amount', 'created_by', 'activate_funding', 'published'], 'integer'],
            [['amount'], 'number', 'min' => '1'],
            [['exchange_rate'], 'number', 'min' => '0.1'],
            [['created_at'], 'safe'],
            [['challenge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Challenge::class, 'targetAttribute' => ['challenge_id' => 'id']],
            [['clone_id'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['clone_id' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['space_id'], 'exist', 'skipOnError' => true, 'targetClass' => Space::class, 'targetAttribute' => ['space_id' => 'id']],
            [['title', 'description', 'city'], 'string', 'max' => 255],
            [['country'], 'string', 'max' => 2],
            [['content'], 'string'],
            [['deadline'], DbDateValidator::class],
            ['youtube_link', function ($attribute) {
                if (null === FundingHelper::getYoutubeEmbedUrl($this->$attribute)) {
                    $this->addError($attribute, Yii::t('XcoinModule.funding', 'Invalid youtube link.'));
                }
            }],
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
                'categories_names',
                'youtube_link',
                'clone_id',
                'picture_file_guid'
            ],
            self::SCENARIO_EDIT => [
                'amount',
                'title',
                'description',
                'content',
                'deadline',
                'country',
                'city',
                'youtube_link',
                'published',
                'activate_funding',
                'categories_names'
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
            'title' => Yii::t('XcoinModule.funding', 'Project Title'),
            'description' => Yii::t('XcoinModule.base', 'Description'),
            'content' => Yii::t('XcoinModule.base', 'Needs & Commitments'),
            'deadline' => Yii::t('XcoinModule.base', 'Deadline'),
            'country' => Yii::t('XcoinModule.base', 'Country'),
            'city' => Yii::t('XcoinModule.base', 'City'),
            'youtube_link' => Yii::t('XcoinModule.base', 'YouTube Video'),
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

    public function afterFind()
    {
        $this->setCategories();
        parent::afterFind();
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
        $challenge = Challenge::getChallengeById($this->challenge_id);
        if ($challenge->acceptSpecificRewardingAsset()) {
            $this->exchange_rate = $challenge->exchange_rate;
        }
        if (!$this->isNewRecord && !$this->challenge->acceptNoRewarding() && (int)$this->amount < (int)$this->getOldAttribute('amount')) {
            $transaction = new Transaction();
            $transaction->transaction_type = Transaction::TRANSACTION_TYPE_TRANSFER;
            $transaction->from_account_id = Account::findOne(['funding_id' => $this->id])->id;
            $transaction->to_account_id = Account::findOne(['space_id' => $this->space_id, 'account_type' => ACCOUNT::TYPE_DEFAULT])->id;;
            $transaction->amount = (int)$this->getOldAttribute('amount') - (int)$this->amount;
            $transaction->asset_id = AssetHelper::getSpaceAsset($this->space)->id;
            if (!$transaction->save()) {
                throw new Exception('Could not create deduction transaction');
            }
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->createFundingAccount();
        }

        if ($this->categories_names && !is_array($this->categories_names)) {
            foreach (explode(",", $this->categories_names) as $category_name) {
                $category = Category::getCategoryByName($category_name);
                if ($category) {
                    $categories[] = $category;
                }
            }
            $this->linkAll('categories', $categories);
        } else {
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
        $fundingAccount = $this->getFundingAccount();

        $fundingAccount->revertTransactions();
        $fundingAccount->archived = Account::ACCOUNT_ARCHIVED;

        $fundingAccount->save();

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

    public function setCategories()
    {
        $categoriesValues = [];

        foreach ($this->getCategories()->all() as $category) {
            $categoriesValues[] = sprintf('%s', $category->name);
        }
        $this->categories_names = $categoriesValues;
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
        return AccountHelper::getFundingRequestedAccountBalance($this);
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
                $this->city,
                $this->youtube_link,
                $this->categories_names
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

    public function getContributors()
    {
        $result = [];

        $targetAmount = $this->getRequestedAmount();
        $fundingAccount = $this->getFundingAccount();

        foreach (Transaction::findAll([
            'to_account_id' => $fundingAccount->id,
            'transaction_type' => Transaction::TRANSACTION_TYPE_TRANSFER
        ]) as $transaction) {
            $account = $transaction->getFromAccount()->one();
            $contentContainer = $account->user;

            if (!$contentContainer) continue;

            $id = $contentContainer->id;

            if (!isset($result[$id])) {
                $result[$id]['record'] = $contentContainer;
                $result[$id]['balance'] = 0;
            }

            $result[$id]['balance'] += $transaction->amount;
            $result[$id]['percent'] = round(($result[$id]['balance'] / $targetAmount) * 100, 4);
        }

        usort($result, function ($a, $b) {
            return $b['balance'] - $a['balance'];
        });

        return $result;
    }

    public function shortenDescription()
    {
        return (strlen($this->description) > 50) ? substr($this->description, 0, 47) . '...' : $this->description;
    }

    public function canInvest()
    {
        if (!$this->challenge->acceptNoRewarding()) {
            return $this->getAvailableAmount() > 0 && $this->getRemainingDays() > 0;
        }

        return true;
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

        if ($transaction->transaction_type === Transaction::TRANSACTION_TYPE_ISSUE) {
            Event::trigger(Transaction::class, Transaction::EVENT_TRANSACTION_TYPE_ISSUE, new Event(['sender' => $transaction]));
        }
    }

    public function isPublished()
    {
        return $this->published == self::FUNDING_PUBLISHED;
    }

    public function isActivated()
    {
        return $this->activate_funding == self::FUNDING_ACTIVATED;
    }

    public function cloneFunding(Funding $clone)
    {
        $this->title = $clone->title;
        $this->description = $clone->description;
        $this->content = $clone->content;
        $this->deadline = $clone->deadline;
        $this->city = $clone->city;
        $this->country = $clone->country;
        $this->youtube_link = $clone->youtube_link;
        $this->amount = $clone->amount;
        $this->exchange_rate = $clone->exchange_rate;
        $this->created_by = $clone->created_by;

        $files = $clone->fileManager->findAll();
        if (!empty($files)) {

            // delete unassigned files before attaching the new file
            foreach (File::findAll(['object_model' => get_class($this), 'object_id' => null]) as $file) {
                $file->delete();
            }

            $picture = new File();
            $picture->file_name = $files[0]->file_name;
            $picture->mime_type = $files[0]->mime_type;
            $picture->size = $files[0]->size;
            $picture->show_in_stream = $files[0]->show_in_stream;

            $picture->save();

            $fileSystem = new Filesystem();
            $fileSystem->mirror(rtrim($files[0]->getStore()->get(), '/file'), rtrim($picture->getStore()->get(), '/file'));

            $this->fileManager->attach($picture);
            $this->picture_file_guid = $picture->guid;
        }

        $categories = [];
        /** @var Category $category */
        foreach ($clone->getCategories()->all() as $category) {
            $categories[$category->name] = $category->name;
        }

        $this->categories_names = $categories;
    }
}
