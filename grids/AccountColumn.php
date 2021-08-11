<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace humhub\modules\xcoin\grids;

use yii\grid\DataColumn;
use humhub\modules\xcoin\models\Account;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\models\Transaction;

/**
 * Description of AccountColumn
 *
 * @author Luke
 */
class AccountColumn extends DataColumn
{

    /**
     * @var \humhub\modules\xcoin\models\Account
     */
    public $accountAttribute;
    public $showIssueWhenEmpty = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->options = ['style' => 'width:220px'];

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function renderDataCellContent($model, $key, $index)
    {
        $account = null;
        if (is_callable($this->accountAttribute)) {
            $account = call_user_func($this->accountAttribute, $model, $key, $index, $this);
        } else {
            $accountAttribute = $this->accountAttribute;
            $account = $model->$accountAttribute;
        }


        if ($account === null && $this->showIssueWhenEmpty) {
            if ($model->transaction_type == Transaction::TRANSACTION_TYPE_ISSUE) {
                return '<span class="label label-info">ISSUE</span>';
            }
        }

        if ($account === null) {
            return '-';
        }
        /* @var $account Account */

        if ($account->space !== null) {
            $img = SpaceImage::widget(['space' => $account->space, 'width' => 26]);
            if ($account->user !== null) {
                $img .= '&nbsp;' . UserImage::widget(['user' => $account->user, 'width' => 26, 'imageOptions' => ['style' => 'margin-top:-3px']]);
            }
        } else {
            $img = UserImage::widget(['user' => $account->user, 'width' => 26]);
        }

        $title = $account->title;

        if ($account->account_type == Account::TYPE_ISSUE) {
            $title = '<span class="label label-warning">ISSUE</span>';
        } elseif ($account->account_type == Account::TYPE_ISSUE) {
            $title = '<span class="label label-info">FUNDING</span>';
        } elseif ($account->account_type == Account::TYPE_DEFAULT) {
            $title = '<span class="label label-default">DEFAULT</span>';
        }


        return $img . '&nbsp;' . $title;
    }

}
