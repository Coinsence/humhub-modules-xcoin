<?php
/**
 * Created by PhpStorm.
 * User: Luke
 * Date: 28.02.2018
 * Time: 09:13
 */

namespace humhub\modules\xcoin\widgets;

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\xcoin\controllers\AjaxController;
use humhub\modules\xcoin\models\Account;
use kartik\widgets\Select2;
use yii\bootstrap\InputWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;

class AccountField extends InputWidget
{
    /** 
    * @var string
    */
    public $to_account;
    
    public function init()
    {
        parent::init();
        $this->value = Html::getAttributeValue($this->model, $this->attribute);
    }


    public function run()
    {
        $output = Html::beginTag('div', ['class' => 'form-group']);

        $output .= '<div class="row">';
        $output .= '<div class="col-md-6">';
        $output .= $this->getOwnerSelect2();
        $output .= '</div>';
        $output .= '<div class="col-md-6">';
        $output .= $this->getSubAccountSelect2();
        $output .= '</div>';
        $output .= '</div>';

        $output .= '<script>
            var subAccountFieldId = "' . $this->getFieldId('accountSelectSubAccount') . '";
            var ownerFieldId = "' . $this->getFieldId('accountSelectOwner') . '";
            var targetFieldId = "' . $this->options['id'] . '";

            $("#" + ownerFieldId).on("select2:select", function(e) {
                    $.ajax({
                        type: "POST",
                        url: "' . Url::to(['/xcoin/ajax/get-sub-accounts']) . '",
                        data: {
                            id: $(this).val()
                        }
                    }).then(function (data) {
                        $("#" + subAccountFieldId).empty();
                        $.each(data, function( index, value ) {
                            var option = new Option(value.title, value.id);
                            $("#" + subAccountFieldId).append(option);
                        });
                        $("#" + subAccountFieldId+"-container").show();
                        $("#" + subAccountFieldId).val(data[0].id);
                        $("#"+targetFieldId).val(data[0].id);
                        $("#" + subAccountFieldId).trigger("change");
                    });
            });
            
            $("#" + subAccountFieldId).on("select2:select", function(e) {
                $("#"+targetFieldId).val($(this).val());
            });
            </script>';

        $output .= Html::endTag('div');

        $output .= Html::activeInput('hidden', $this->model, $this->attribute, $this->options);

        if (empty($this->value)) {
            // Hide Sub Account Select when no account id is selected
            $output .= '<script>$("#' . $this->getFieldId('accountSelectSubAccount') . '-container").hide();</script>';
        }

        return $output;
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    public function getOwnerSelect2()
    {
        $data = null;
        $value = null;
        if (!empty($this->value)) {
            $account = Account::findOne(['id' => (int)$this->value]);
            if ($account !== null) {
                $data = [AjaxController::getOwnerInfo(($account->space !== null) ? $account->space : $account->user)];
                $value = ($account->space !== null) ? $account->space->contentcontainer_id : $account->user->contentcontainer_id;
            }
        }

        return Html::tag('div', Select2::widget([
            'name' => 'accountSelectOwner',
            'id' => $this->getFieldId('accountSelectOwner'),
            'theme' => Select2::THEME_BOOTSTRAP,
            'options' => ['placeholder' => '- Select owner -'],
            'value' => $value,
            'pluginOptions' => [
                'data' => $data,
                'allowClear' => false,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Error while loading the results...'; }"),
                ],
                'ajax' => [
                    'url' => Url::to(['/xcoin/ajax/get-accounts']),
                    'dataType' => 'json',
                    'delay' => 250,
                    'cache' => true,
                    'data' => new JsExpression('function(params) { return {q:params.term}; }')
                ],
                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                'templateResult' => new JsExpression('function(a) { if (!a.id && a.text) { return a.text}; return a.image + "&nbsp;" + a.displayname; } '),
                'templateSelection' => new JsExpression('function(b) { if (!b.id && b.text) { return b.text}; return b.image + "&nbsp;" + b.displayname;  }'),
            ]
        ]));
    }

    /**
     * @throws \Exception
     */
    public function getSubAccountSelect2()
    {

        $data = [];

        // Lookup initial array if value exists
        if (!empty($this->value)) {
            $account = Account::findOne(['id' => (int)$this->value]);
            if ($account !== null) {
                /** @var ContentContainerActiveRecord $ccAr */
                $ccAr = ($account->space !== null) ? $account->space : $account->user;
                $contentContainer = $ccAr->contentContainerRecord;
                if ($contentContainer !== null) {
                    $data = AjaxController::getSubAccounts($contentContainer);
                }
            }
        }

        return Html::tag('div', Select2::widget([
            'name' => 'accountSelectSubAccount',
            'id' => $this->getFieldId('accountSelectSubAccount'),
            'theme' => Select2::THEME_BOOTSTRAP,
            'value' => $this->value,
            'options' => ['placeholder' => '- Select account -'],
            'data' => ArrayHelper::map($data, 'id', 'title'),
            'pluginOptions' => [
                'allowClear' => false,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Error while loading the results...'; }"),
                ],
                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                'templateResult' => new JsExpression('function(a) {  return a.text; }'),
                'templateSelection' => new JsExpression('function(b) { return b.text; }'),
            ]
        ]), ['id' => $this->getFieldId('accountSelectSubAccount') . '-container']);
    }


    protected function getFieldId($fieldName)
    {
        return crc32($this->model->className() . $this->attribute . $fieldName);
    }

}