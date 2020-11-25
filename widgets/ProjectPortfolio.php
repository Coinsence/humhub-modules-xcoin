<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\xcoin\widgets;

use humhub\modules\user\models\User;
use humhub\modules\activity\models\Activity;
use humhub\modules\post\models\Post;
use humhub\modules\space\models\Space;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\helpers\AssetHelper;
use humhub\modules\xcoin\helpers\ChallengeHelper;
use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\Funding;
use humhub\components\Controller;
use humhub\modules\xcoin\models\FundingFilter;
use humhub\modules\xcoin\widgets\ChallengeImage;
        use humhub\modules\user\models\Follow ; 
        use humhub\modules\tasks\models\Task  ;    
        use humhub\modules\comment\models\Comment ;
        use humhub\modules\like\models\Like       ;
        use humhub\modules\polls\models\Poll   ;
use Yii;
use yii\db\Expression;

/**
 * Displays the profile header of a user
 *
 * @since 0.5
 * @author Luke
 */
class ProjectPortfolio extends \yii\base\Widget
{

    /**
     * @var User
     */
    public $user;
    public $fundings;
    public $activities;

    /**
     * @var boolean is owner of the current profile
     */
    protected $isProfileOwner = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        /**
         * Try to autodetect current user by controller
         */
      
       
        $allActivities=[];  
      
        $classArray = array(
            Post::class => 'created_by',
            Follow::class => 'user_id',
            Task::class=>'created_by',
            Comment::class=>'created_by',
            // Like::class=>'created_by',
            Poll::class=>'created_by',
            Space::class=>'created_by'
        );
        
        $this->activities=$allActivities;
        
        

        
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $query = Funding::find();
        $query->where(['>', 'xcoin_funding.amount', 0]);
        $query->andWhere(['or',
            ['xcoin_funding.status'=> Funding::FUNDING_STATUS_IN_PROGRESS],
            ['xcoin_funding.status'=> Funding::FUNDING_STATUS_INVESTMENT_RESTARTED]
        ]); // only not investment accepted campaigns
        $query->andWhere(['IS NOT', 'xcoin_funding.id', new Expression('NULL')]);
        $query->orderBy(['created_at' => SORT_DESC]);
        $query->andWhere(['review_status' => Funding::FUNDING_REVIEWED]);
        $query->innerJoin('xcoin_challenge', 'xcoin_funding.challenge_id = xcoin_challenge.id');
        $query->andWhere('xcoin_challenge.status = 1');
        $query->andWhere('xcoin_challenge.stopped = 0');

        $model = new FundingFilter();
        $challengeId = null;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            if ($model->asset_id)
                $query->andWhere(['xcoin_challenge.asset_id' => $model->asset_id]);
            if ($model->categories) {
                $query->joinWith('categories category');
                $query->andWhere(['category.id' => $model->categories]);
            }

            if ($model->country)
                $query->andWhere(['country' => $model->country]);
            if ($model->city)
                $query->andWhere(['like', 'city', $model->city . '%', false]);
            if ($model->keywords)
                $query->andWhere(['like', 'xcoin_funding.title', '%' . $model->keywords . '%', false]);

        } else if ($challengeId) {
            $query->andWhere(['challenge_id' => $challengeId]);
        }

        if ($challengeId) {
            $challengesList = Challenge::findAll(['id' => $challengeId]);
        } else {
            $challengesList = Challenge::findAll(['status' => Challenge::CHALLENGE_STATUS_ENABLED, 'stopped' => Challenge::CHALLENGE_ACTIVE]);
        }

        $assetsList = [];
        $countriesList = [];

        foreach ($challengesList as $challenge) {
            $asset = $challenge->asset;
            $space = $challenge->asset->space;
            $assetsList[$asset->id] = SpaceImage::widget(['space' => $space, 'width' => 16, 'showTooltip' => true, 'link' => true]) . ' ' . $space->name;
        }

        $challenge = Challenge::findOne(['id' => $challengeId]);
        if (!Yii::$app->user->isGuest && Yii::$app->useR->id == $this->user->id) {
            $this->isProfileOwner = true;
        }
        return $this->render('projectPortfolio', [
            'selectedChallenge' => $challenge,
            'model' => $model,
            'assetsList' => $assetsList,
            'countriesList' => $countriesList,
            'fundings' => $query->all(),
            'user' => $this->user,
            'isProfileOwner' => $this->isProfileOwner,
            'myActivities'=>$this->activities,
            'challengesCarousel' => ChallengeHelper::getRandomChallenges()
        ]);
        
    }
}

?>

