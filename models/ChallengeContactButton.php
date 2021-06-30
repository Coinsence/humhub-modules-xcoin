<?php
/**
 * Created By IJ
 * @author : Ala Daly <rafin_ala03@hotmail.fr>
 * @date : 11‏/5‏/2021, Tue
 **/

namespace humhub\modules\xcoin\models;

use humhub\components\ActiveRecord;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "xcoin_challenge_contact_button".
 *
 * @property integer $id
 * @property integer $challenge_id
 * @property integer $status
 * @property string $button_title
 * @property string $popup_text
 * @property string $receiver
 * @property Challenge $challenge
 */
class ChallengeContactButton extends ActiveRecord
{
    const SCENARIO_CREATE = 'screate';
    const SCENARIO_EDIT = 'sedit';

    const CONTACT_BUTTON_DISABLED = 0;
    const CONTACT_BUTTON_ENABLED = 1;

    const PROJECT_OWNER_RECEIVER = "project";
    const CHALLENGE_OWNER_RECEIVER = "challenge";


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_challenge_contact_button';
    }

    public function rules()
    {

        return [
            [['challenge_id', 'status'], 'required'],
            [['challenge_id', 'status'], 'integer'],
            [['challenge_id'], 'exist', 'skipOnError' => true, 'targetClass' => Challenge::class, 'targetAttribute' => ['challenge_id' => 'id']],
            [['button_title'], 'string', 'max' => 255],
            [['receiver'], 'string', 'max' => 255],
            [['popup_text'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('XcoinModule.challengeButton', 'ID'),
            'button_title' => Yii::t('XcoinModule.challengeButton', 'Button title'),
            'popup_text' => Yii::t('XcoinModule.challengeButton', 'Popup text'),
            'receiver' => Yii::t('XcoinModule.challenge', 'Receiver'),
        ];

    }

    /**
     * @return ActiveQuery
     */
    public function getChallenge()
    {
        return $this->hasOne(Challenge::class, ['id' => 'challenge_id']);
    }

    public function isButtonEnabled()
    {
        return $this->status == self::CONTACT_BUTTON_ENABLED;
    }


}
