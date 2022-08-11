<?php

namespace humhub\modules\xcoin\grids;

use Yii;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\libs\ActionColumn;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\Transaction;

/**
 * Description of LatestTransactionsGridView
 *
 * @author Luke
 * @contributer Daly Ghaith <daly.ghaith@gmail.com>
 */
class TransactionsGridView extends GridView
{

    /**
     * @var \humhub\modules\content\components\ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @var Account
     */
    public $account;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $transactions =
        $query = Transaction::find();
        $query->andWhere(['from_account_id' => $this->account->id]);
        $query->orWhere(['to_account_id' => $this->account->id]);
        $query->addOrderBy(['id' => SORT_DESC]);

        $this->dataProvider = new ActiveDataProvider([
            'query' => $query,
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
                'options' => ['style' => 'width:180px'],
                'format' => 'raw',
                'value' => function($model) {
                    return Yii::$app->formatter->asDateTime($model->created_at, 'short');
                }
            ],
            [
                'attribute' => 'amount',
                'label' => Yii::t('XcoinModule.base', 'Amount'),
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                'value' => function($model) {
                    if ($model->from_account_id == $this->account->id) {
                        return '<span style="color:red;font-weight:bold">-' . $model->amount . '</span>';
                    } else {
                        return '<span style="color:green;font-weight:bold">+' . $model->amount . '</span>';
                    }
                }
            ],
            [
                'attribute' => 'asset_id',
                'label' => Yii::t('XcoinModule.base', 'Asset'),
                'format' => 'raw',
                'options' => ['style' => 'width:120px'],
                'value' => function($model) {

                    return SpaceImage::widget(['space' => $model->asset->space, 'width' => 26, 'link' => true, 'showTooltip' => true]);
                }
            ],
            [
                'format' => 'raw',
                'value' => function($model) {
                    return '';
                }
            ],
            [
                'class' => AccountColumn::class,
                'accountAttribute' => function($model) {
                    if ($model->from_account_id == $this->account->id) {
                        return $model->toAccount;
                    } else {
                        return $model->fromAccount;
                    }
                },
                'label' => Yii::t('XcoinModule.base', 'Related account')
            ],
            [
                'class' => ActionColumn::class,
                'actions' => function($model) {
                    $actions = [];
                    $actions['Show transaction details'] = ['/xcoin/transaction/details', 'container' => $this->contentContainer, 'linkOptions' => ['data-target' => '#globalModal']];
                    $actions[] = '---';
                    $relatedAccountId = ($model->from_account_id != $this->account->id) ? $model->from_account_id : $model->to_account_id;
                    $actions['Open related account'] = ['/xcoin/account', 'id' => $relatedAccountId, 'container' => $this->contentContainer];
                    return $actions;
                }
            ],
        ];


        parent::init();
    }

}
