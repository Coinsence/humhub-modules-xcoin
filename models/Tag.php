<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2020 Coinsence
 * @license https://www.humhub.com/licences
 *
 * @author Daly Ghaith <daly.ghaith@gmail.com>
 */

namespace humhub\modules\xcoin\models;

use Yii;
use humhub\components\ActiveRecord;
use humhub\modules\file\models\File;
use humhub\modules\user\models\User;

/**
 * This is the model class for table "xcoin_tag".
 *
 * @property integer $id
 * @property string $name
 * @property integer $type
 * @property string $created_at
 * @property integer $created_by
 */
class Tag extends ActiveRecord
{
    const SCENARIO_CREATE = 'screate';
    const SCENARIO_EDIT = 'sedit';

    // tag types
    const TYPE_SPACE = 1;
    const TYPE_USER = 2;
    const TYPE_ALL_SPACES = 3;
    const TYPE_ALL_USERS = 4;

    public $pictureFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_tag';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type', 'created_by'], 'required', 'when' => function($model) {
                return !in_array($model->type, [self::TYPE_ALL_SPACES, self::TYPE_ALL_USERS]);
            }],
            [['type', 'created_by'], 'integer'],
            ['type', 'validateType'],
            [['created_at'], 'safe'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['name'], 'string', 'max' => 255],
            [['name'], 'unique', 'targetAttribute' => ['name', 'type'], 'message' => Yii::t('XcoinModule.tag', 'the combination {attributes} is already used ')]
        ];
    }

    public function validateType($attribute, $params, $validator)
    {
        if (!in_array($this->$attribute, [self::TYPE_SPACE, self::TYPE_USER, self::TYPE_ALL_SPACES, self::TYPE_ALL_USERS])) {
            $this->addError($attribute, Yii::t('XcoinModule.tag', 'The type must be either "Space" or "User".'));
        }
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'name',
                'type'
            ],
            self::SCENARIO_EDIT => [
                'name'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('XcoinModule.tag', 'ID'),
            'name' => Yii::t('XcoinModule.tag', 'Name'),
        ];
    }

    public static function getTypes()
    {
        return [
            self::TYPE_SPACE => Yii::t('XcoinModule.tag', 'Space'),
            self::TYPE_USER => Yii::t('XcoinModule.tag', 'User'),
        ];
    }

    public function canDeleteFile()
    {
        return Yii::$app->user->identity->isSystemAdmin() ? true : false;
    }

    public function getCover()
    {
        return File::find()->where([
            'object_model' => Tag::class,
            'object_id' => $this->id
        ])->orderBy(['id' => SORT_DESC])->one();
    }

    public static function getTagByName($tagName)
    {
        return Tag::find()->where(['name' => $tagName])->one();
    }
}
