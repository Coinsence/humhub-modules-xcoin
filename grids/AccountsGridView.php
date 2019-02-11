<?php

namespace humhub\modules\xcoin\grids;

use yii\bootstrap\Html;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use humhub\modules\xcoin\helpers\TransactionHelper;
use humhub\modules\xcoin\models\Account;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\grids\AccountColumn;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\libs\ActionColumn;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;

/**
 * Description of LatestTransactionsGridView
 *
 * @author Luke
 */
class AccountsGridView extends GridView
{

    public $contentContainer;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->dataProvider = new ActiveDataProvider([
            'query' => AccountHelper::getAccountsQuery($this->contentContainer),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        $this->columns = [
            [
                'attribute' => 'space_id',
                'label' => 'Owner',
                'format' => 'raw',
                'options' => ['style' => 'width:35px'],
                'visible' => (!$this->contentContainer instanceof Space),
                'value' => function($model) {
                    if ($model->space !== null) {
                        return SpaceImage::widget(['space' => $model->space, 'width' => 26]);
                    }

                    return '-';
                }
            ],
            [
                'attribute' => 'user_id',
                'label' => 'Manager',
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                'visible' => (!$this->contentContainer instanceof User),
                'value' => function($model) {
                    if ($model->user === null) {
                        return '-';
                    }
                    return UserImage::widget(['user' => $model->user, 'width' => 26]);
                }
            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->account_type == Account::TYPE_ISSUE) {
                        return '<span class="label label-info">ISSUES</span>';
                    }
                    if ($model->account_type == Account::TYPE_FUNDING) {
                        return '<span class="label label-info">FUNDINGS</span>';
                    }
                    if ($model->account_type == Account::TYPE_DEFAULT) {
                        return '<span class="label label-info">DEFAULT</span>';
                    }
                    if ($model->account_type == Account::TYPE_TASK) {
                        return '<span class="label label-info">TASK</span> '.
                            Html::a(
                                Html::encode($model->title),
                                [
                                    '/tasks/task/view',
                                    'id' => $model->task->id,
                                    'container' => $this->contentContainer
                                ]
                            );
                    }
                    return Html::encode($model->title);
                }
            ],
            [
                'label' => 'Asset(s) balance',
                'format' => 'raw',
                'value' => function($model) {
                    $list = [];
                    foreach ($model->getAssets() as $asset) {
                        $list[] = '<strong>' . $model->getAssetBalance($asset) . '</strong>&nbsp; ' .
                                SpaceImage::widget(['space' => $asset->space, 'width' => 20, 'showTooltip' => true, 'link' => true]) . '</span>';
                    }
                    return implode('&nbsp;&nbsp;&middot;&nbsp;&nbsp;', $list);
                }
            ],
            [
                'format' => 'raw',
                'options' => ['style' => 'width:120px;text-align:right'],
                'contentOptions' => ['style' => 'text-align:right'],
                'value' => function ($model) {

                    $transferButton = '';
                    if (AccountHelper::canManageAccount($model) && Account::TYPE_TASK != $model->account_type) {
                        $transferButton = Html::a('<i class="fa fa-exchange" aria-hidden="true"></i>', ['/xcoin/transaction/transfer', 'accountId' => $model->id, 'container' => $this->contentContainer], ['class' => 'btn btn-default', 'data-target' => '#globalModal']) . '&nbsp;';
                    }

                    return $transferButton . Html::a('<i class="fa fa-search" aria-hidden="true"></i>', ['/xcoin/account', 'id' => $model->id, 'container' => $this->contentContainer], ['class' => 'btn btn-default']);
                }
            ],
        ];


        parent::init();
    }

}
