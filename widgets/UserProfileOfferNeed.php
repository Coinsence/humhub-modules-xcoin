<?php

namespace humhub\modules\xcoin\widgets;

use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\ProfileOfferNeed;
use Yii;
use yii\base\Widget;
use yii\db\Expression;

/**
 * profile_offer_need Widget
 *
 * Render profile_offer_need bloc
 */
class UserProfileOfferNeed extends Widget
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var array html options for the generated experience bloc
     */
    public $htmlOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return $this->render('@xcoin/widgets/views/user-profile-offer-need', [
            'profileOfferNeeds' => ProfileOfferNeed::find()
                ->where(['user_id' => $this->user->id])
                ->all(),
            'htmlOptions' => $this->htmlOptions,
            'user' => $this->user
        ]);
    }
}
