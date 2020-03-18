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
 * @property string $created_at
 * @property integer $created_by
 */
class Category extends ActiveRecord
{

    const SCENARIO_CREATE = 'screate';
    const SCENARIO_EDIT = 'sedit';

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
            [['name', 'slug', 'created_by'], 'required'],
            [['created_by'], 'integer'],
            [['created_at'], 'safe'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['name', 'slug'], 'string', 'max' => 255],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CREATE => [
                'name',
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
