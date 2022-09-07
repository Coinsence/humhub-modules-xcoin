<?php
/**
 * Created By IJ
 * @author : Ala Daly <ala.daly@dotit-corp.com>
 * @date : 7‏/9‏/2022, Wed
 **/

namespace humhub\modules\xcoin\grids;


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
                'attribute' => 'asset_id',
                'label' => Yii::t('XcoinModule.base', 'Asset'),
                'format' => 'raw',
                'options' => ['style' => 'width:120px'],
                'value' => function ($model) {

                    return SpaceImage::widget(['space' => $model->asset->space, 'width' => 26, 'link' => true, 'showTooltip' => true]);
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

                    return $model->account;
                },
                'label' => Yii::t('XcoinModule.base', 'Related account')
            ],

        ];

        parent::init();
    }

}
