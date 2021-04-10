<?php
/**
 * Created by Mortadha Ghanmi.
 * Email: mortadha.ghanmi56@gmail.com
 */

namespace humhub\modules\xcoin\models;


use Yii;
use yii\base\Model;

class SpaceModuleAppearanceSettings extends Model
{
    /**
     * @var boolean
     */
    public $partiallyHideCover;

    /**
     * @var \humhub\modules\space\models\Space Space on which these settings are for
     */
    public $space;

    public function init() {
        $module = Yii::$app->getModule('xcoin');

        if (null !== $partiallyHideCover = $module->settings->space()->get('partiallyHideCover')) {
            $this->partiallyHideCover = $partiallyHideCover;
        } else {
            $this->partiallyHideCover = 1;
        }
    }

    /**
     * Static initializer
     * @return \self
     */
    public static function instantiate()
    {
        return new self;
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['partiallyHideCover'], 'boolean'],
        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [
            'partiallyHideCover' => Yii::t('XcoinModule.config', 'Hide space cover'),
        ];
    }

    public function save()
    {
        if (!$this->validate()) {
            return false;
        }

        $module = Yii::$app->getModule('xcoin');
        $module->settings->space()->set('partiallyHideCover', $this->partiallyHideCover);

        return true;
    }
}
