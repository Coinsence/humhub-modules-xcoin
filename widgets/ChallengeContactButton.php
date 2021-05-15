<?php
/**
 * Created By IJ
 * @author : Ala Daly <ala.daly@dotit-corp.com>
 * @date : 12‏/5‏/2021, Wed
 **/

namespace humhub\modules\xcoin\widgets;


use humhub\components\Widget;
use humhub\modules\mail\models\forms\CreateMessage;
use humhub\modules\xcoin\models\Challenge;
use humhub\modules\xcoin\models\Funding;
use yii\base\DynamicModel;
use yii\helpers\Html;
use yii\web\UploadedFile;

class ChallengeContactButton extends Widget
{
    /**
     * @var Funding
     */
    public $funding;

    /**
     * @var ChallengeContactButton
     */
    public $contactButton;


    /**
     * @var CreateMessage
     */
    public $model;

    public function init()
    {
        parent::init();

    }

    public function run()
    {
        return $this->render('@xcoin/widgets/views/challenge-contact-button', [
            'contactButton' => $this->contactButton,
            'funding' => $this->funding,
            'model' => $this->model,
        ]);
    }
}