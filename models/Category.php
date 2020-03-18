<?php
/**
 * @link https://coinsence.org/
 * @copyright Copyright (c) 2018 Coinsence
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
 * This is the model class for table "xcoin_category".
 *
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property integer $type
 * @property string $created_at
 * @property integer $created_by
 */
class Category extends ActiveRecord
{

    const SCENARIO_CREATE = 'screate';
    const SCENARIO_EDIT = 'sedit';

    // category types
    const TYPE_FUNDING = 1;
    const TYPE_MARKETPLACE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'xcoin_category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'slug', 'type', 'created_by'], 'required'],
            [['type', 'created_by'], 'integer'],
            ['type', 'validateType'],
            [['created_at'], 'safe'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['name', 'slug'], 'string', 'max' => 255],
        ];
    }

    public function validateType($attribute, $params, $validator)
    {
        if (!in_array($this->$attribute, [self::TYPE_FUNDING, self::TYPE_MARKETPLACE])) {
            $this->addError($attribute, Yii::t('XcoinModule.category', 'The type must be either "Crowdfunding" or "Marketplace".'));
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
            'id' => Yii::t('XcoinModule.category', 'ID'),
            'name' => Yii::t('XcoinModule.category', 'Name'),
        ];
    }

    public static function getTypes()
    {
        return [
            self::TYPE_FUNDING => Yii::t('XcoinModule.category', 'Crowdfunding'),
            self::TYPE_MARKETPLACE => Yii::t('XcoinModule.category', 'Marketplace'),
        ];
    }

    public function canDeleteFile()
    {
        return Yii::$app->user->identity->isSystemAdmin() ? true : false ;
    }

    public function getCover()
    {
        return File::find()->where([
            'object_model' => Category::class,
            'object_id' => $this->id
        ])->orderBy(['id' => SORT_DESC])->one();
    }
}
