<?php

namespace humhub\modules\xcoin\grids;

use Yii;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use humhub\modules\xcoin\helpers\TransactionHelper;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\xcoin\grids\AccountColumn;
use humhub\libs\ActionColumn;

/**
 * Description of LatestTransactionsGridView
 *
 * @author Luke
 */
class LatestTransactionsGridView extends GridView
{

    public $contentContainer;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->dataProvider = new ActiveDataProvider([
            'query' => TransactionHelper::getLatest($this->contentContainer),
            'pagination' => [
                'pageSize' => 30,
            ],
        ]);

        $this->dataProvider->pagination->pageParam = 'ltg-page';
        $this->dataProvider->sort->sortParam = 'ltg-sort';


        $this->columns = [
            [
                'attribute' => 'created_at',
                'label' => Yii::t('XcoinModule.base', 'Date'),
                'options' => ['style' => 'width:180px']
            ],
            [
                'attribute' => 'amount',
                'label' => Yii::t('XcoinModule.base', 'Amount'),
                'format' => 'raw',
                'options' => ['style' => 'width:120px'],
                'value' => function($model) {

                    return $model->amount . '&nbsp;&nbsp;' . SpaceImage::widget(['space' => $model->asset->space, 'width' => 26, 'showTooltip' => true, 'link' => true]);
                }
            ],
            [
                'class' => AccountColumn::class,
                'accountAttribute' => 'fromAccount',
                'label' => Yii::t('XcoinModule.base', 'Sender'),
                'showIssueWhenEmpty' => true
            ],
            [
                'class' => AccountColumn::class,
                'accountAttribute' => 'toAccount',
                'label' => Yii::t('XcoinModule.base', 'Recipient')
            ],
            [
                'class' => ActionColumn::class,
                'actions' => function($model) {
                    $actions = [];
                    $actions['Show transaction details'] = ['/xcoin/transaction/details', 'container' => $this->contentContainer, 'linkOptions' => ['data-target' => '#globalModal']];
                    $actions[] = '---';
                    $actions['Open sender account'] = ['/xcoin/account', 'id' => $model->from_account_id, 'container' => $this->contentContainer];
                    $actions['Open recipient account'] = ['/xcoin/account', 'id' => $model->to_account_id, 'container' => $this->contentContainer];
                    return $actions;
                }
            ],
        ];


        parent::init();
    }

}
