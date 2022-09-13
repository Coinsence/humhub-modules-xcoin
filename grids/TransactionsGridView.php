<?php

namespace humhub\modules\xcoin\grids;

use humhub\modules\algorand\calls\Coin;
use humhub\modules\algorand\utils\Helpers;
use humhub\modules\xcoin\models\Asset;
use Yii;
use humhub\widgets\GridView;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\libs\ActionColumn;
use humhub\modules\xcoin\models\Account;
use yii\data\ArrayDataProvider;

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
        $transactions = Coin::transactionsList($this->account);

        $transactions = array_filter($transactions, function ($transaction) {
            return property_exists($transaction, 'asset-transfer-transaction');
        });

        $this->dataProvider = new ArrayDataProvider([
            'allModels' => $transactions,
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
                    return Yii::$app->formatter->asDateTime($model->date, 'short');
                }
            ],
            [
                'attribute' => 'amount',
                'label' => Yii::t('XcoinModule.base', 'Amount'),
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                'value' => function($model) {
                    $fromAccount = Account::findOne(['algorand_address' => $model->fromAccount]);
                    if ($fromAccount->id == $this->account->id) {
                        return '<span style="color:red;font-weight:bold">-' . Helpers::formatCoinAmount($model->{'asset-transfer-transaction'}->amount, true) . '</span>';
                    } else {
                        return '<span style="color:green;font-weight:bold">+' . Helpers::formatCoinAmount($model->{'asset-transfer-transaction'}->amount, true) . '</span>';
                    }
                }
            ],
            [
                'attribute' => 'asset_id',
                'label' => Yii::t('XcoinModule.base', 'Asset'),
                'format' => 'raw',
                'options' => ['style' => 'width:120px'],
                'value' => function($model) {
                    $transaction = Coin::transaction($model->txID);
                    $asset = Asset::findOne(['algorand_asset_id' => $transaction->{'asset-transfer-transaction'}->{'asset-id'}]);

                    return SpaceImage::widget(['space' => $asset->space, 'width' => 26, 'link' => true, 'showTooltip' => true]);
                }
            ],
            [
                'class' => AccountColumn::class,
                'accountAttribute' => function($model) {
                    $fromAccount = Account::findOne(['algorand_address' => $model->fromAccount]);
                    if ($fromAccount->id == $this->account->id) {
                        return Account::findOne(['algorand_address' => $model->toAccount]);
                    } else {
                        return $fromAccount;
                    }
                },
                'label' => Yii::t('XcoinModule.base', 'Related account')
            ],
            [
                'class' => ActionColumn::class,
                'actions' => function($model) {
                    $fromAccount = Account::findOne(['algorand_address' => $model->fromAccount]);
                    $toAccount =  Account::findOne(['algorand_address' => $model->toAccount]);

                    $actions = [];
                    $actions['Show transaction details'] = ['/xcoin/transaction/details', 'id'  => $model->txID, 'container' => $this->contentContainer, 'linkOptions' => ['data-target' => '#globalModal']];
                    $actions[] = '---';
                    $relatedAccountId = ($fromAccount->id != $this->account->id) ? $fromAccount->id : $toAccount->id;
                    $actions['Open related account'] = ['/xcoin/account', 'id' => $relatedAccountId, 'container' => $this->contentContainer];
                    return $actions;
                }
            ],
        ];


        parent::init();
    }

}
