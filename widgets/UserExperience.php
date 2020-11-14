<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\widgets;

use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Experience;
use Yii;
use yii\base\Widget;

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
            'experiences' => Experience::find()->where(['user_id' => $this->user->id])->orderBy(['id' => SORT_DESC])->all(),
            'htmlOptions' => $this->htmlOptions,
            'user' => $this->user
        ]);
    }
}
