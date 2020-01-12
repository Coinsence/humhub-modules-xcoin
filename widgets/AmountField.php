<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\xcoin\widgets;

use humhub\modules\space\widgets\Image as SpaceImage;
use yii\widgets\InputWidget;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;

/**
 * Description of AmountField
 *
 * @author Luke
 */
class AmountField extends InputWidget
{

    /**
     * @var array the HTML attributes for the input tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [];

    /**
     * @var \humhub\modules\xcoin\models\Asset
     */
    public $asset;
    public $type = 'text';
    public $readonly = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        Html::addCssClass($this->options, 'form-control');
        $this->options['type'] = 'number';
        $this->options['min'] = '0';
        $this->options['step'] = '0.1';
        $this->options['readonly'] = $this->readonly;

        $this->field->template = '{label}{beginWrapper}<div class="input-group">
          <span class="input-group-addon">' . SpaceImage::widget(['space' => $this->asset->space, 'width' => 25]) . '</span>
          {input}</div>{hint}{error}{endWrapper}';
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            echo Html::activeInput($this->type, $this->model, $this->attribute, $this->options);
        } else {
            echo Html::input($this->type, $this->name, $this->value, $this->options);
        }
    }

}
