<?php
/**
 * Created By IJ
 * @author : Ala Daly <ala.daly@dotit-corp.com>
 * @date : 7‏/9‏/2022, Wed
 **/

namespace humhub\modules\xcoin\grids;


use humhub\libs\ActionColumn;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\models\AccountVoucher;
use humhub\modules\xcoin\models\Transaction;
use humhub\widgets\GridView;
use Yii;
use yii\data\ActiveDataProvider;
use humhub\modules\space\widgets\Image as SpaceImage;

class AccountVouchersGridView extends GridView
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
        $query = AccountVoucher::find();
        $query->andWhere(['account_id' => $this->account->id]);
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
                'attribute' => 'amount',
                'label' => Yii::t('XcoinModule.base', 'Amount'),
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                'value' => function ($model) {
                    return '<span style="font-weight:bold">' . $model->amount . '</span>';
                }
            ],
            [
                'attribute' => 'value',
                'label' => Yii::t('XcoinModule.base', 'Value'),
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                'value' => function ($model) {
                    return '<span style="font-weight:bold">' . $model->value . '</span>';
                }
            ],
            [
                'attribute' => 'tag',
                'label' => Yii::t('XcoinModule.base', 'Tag'),
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                'value' => function ($model) {
                    return '<span style="font-weight:bold">' . $model->tag . '</span>';
                }
            ],
            [
                'attribute' => 'asset_id',
                'label' => Yii::t('XcoinModule.base', 'Asset'),
                'format' => 'raw',
                'options' => ['style' => 'width:120px'],
                'value' => function ($model) {

                    return SpaceImage::widget(['space' => $model->asset->space, 'width' => 26, 'link' => true, 'showTooltip' => true]);
                }
            ],
            [
                'attribute' => 'status',
                'label' => Yii::t('XcoinModule.base', 'Status'),
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                'value' => function ($model) {
                    if ($model->status == AccountVoucher::STATUS_USED) {
                        return '<span style="color:red;font-weight:bold">' . Yii::t('XcoinModule.base', 'REDEEMED') . '</span>';
                    } elseif ($model->status == AccountVoucher::STATUS_READY) {
                        return '<span style="color:green;font-weight:bold">' . Yii::t('XcoinModule.base', 'AVAILABLE') . '</span>';
                    } else {
                        return '<span style="color:orange;font-weight:bold">' . Yii::t('XcoinModule.base', 'DISABLED') . '</span>';

                    }
                }
            ],
            [
                'format' => 'raw',
                'value' => function ($model) {
                    return '';
                }
            ],
            [
                'class' => AccountColumn::class,
                'accountAttribute' => function ($model) {

                    return $model->getRedeemedAccount()->one();
                },
                'label' => Yii::t('XcoinModule.base', 'Related account')
            ],
            [
                'class' => ActionColumn::class,
                'actions' => function ($model) {
                    if ($model->status == AccountVoucher::STATUS_USED) {
                        $actions = [];
                        return $actions;

                    } else {
                        $actions = [];
                        $actions['Disable/Enable'] = ['/xcoin/account/enable-voucher', 'voucherId' => $model->id, 'accountId' => $this->account->id, 'container' => $this->contentContainer];
                        return $actions;
                    }
                },
            ],

        ];

        parent::init();
    }

}
