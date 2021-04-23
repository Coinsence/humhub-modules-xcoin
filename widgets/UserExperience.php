<?php

namespace humhub\modules\xcoin\widgets;

use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Experience;
use Yii;
use yii\base\Widget;
use yii\db\Expression;

/**
 * UserExperience Widget
 *
 * Render user experiences bloc
 */
class UserExperience extends Widget
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
        return $this->render('@xcoin/widgets/views/user-experience', [
            'experiences' => Experience::find()
                ->where(['user_id' => $this->user->id])
                ->orderBy([ new Expression('end_date IS NULL desc, end_date desc')])
                ->all(),
            'htmlOptions' => $this->htmlOptions,
            'user' => $this->user
        ]);
    }
}
