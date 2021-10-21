<?php

namespace humhub\modules\xcoin\grids;

use Yii;
use humhub\libs\Html;
use humhub\modules\space\models\Space;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\user\models\User;
use humhub\modules\xcoin\models\Account;
use humhub\modules\xcoin\widgets\PurchaseCoin;

/**
 * Description of LatestTransactionsGridView
 *
 * @author Luke
 */
class SenderAccountGridView extends GridView
{

    public $contentContainer;
    public $nextRoute;
    public $requireAsset;
    public $disableAccount;
    public $product;

    /**
     * @inheritdoc
     */
    public function init()
    {

        $contentContainer = $this->contentContainer;
        if ($this->contentContainer instanceof User && $contentContainer->id != Yii::$app->user->id) {
            $contentContainer = Yii::$app->user->getIdentity();
        }

        $this->dataProvider = new ActiveDataProvider([
            'query' => AccountHelper::getAccountsQuery($contentContainer, $this->requireAsset),
            'pagination' => false
        ]);

        $this->columns = [
            [
                'attribute' => 'space_id',
                'label' => Yii::t('XcoinModule.base', 'Owner'),
                'format' => 'raw',
                'options' => ['style' => 'width:35px'],
                'visible' => (!$this->contentContainer instanceof Space),
                'value' => function ($model) {
                    if ($model->space !== null) {
                        return SpaceImage::widget(['space' => $model->space, 'width' => 26]);
                    }

                    return UserImage::widget(['user' => $model->user, 'width' => 26]);
                }
            ],
            [
                'attribute' => 'user_id',
                'label' => Yii::t('XcoinModule.base', 'Manager'),
                'format' => 'raw',
                'options' => ['style' => 'width:80px'],
                'visible' => (!$this->contentContainer instanceof User),
                'value' => function ($model) {
                    if ($model->user === null) {
                        return SpaceImage::widget(['space' => $model->space, 'width' => 26]);
                    }

                    return UserImage::widget(['user' => $model->user, 'width' => 26]);
                }
            ],
            [
                'attribute' => 'title',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->account_type == Account::TYPE_ISSUE) {
                        return '<span class="label label-info">ISSUES</span>';
                    }
                    if ($model->account_type == Account::TYPE_FUNDING) {
                        return $model->funding ?
                            '<span class="label label-info">FUNDINGS</span> ' .
                            Html::a(
                                Html::encode($model->title),
                                [
                                    '/xcoin/funding/overview',
                                    'fundingId' => $model->funding->id,
                                    'container' => $this->contentContainer
                                ]
                            ) :
                            '<span class="label label-danger">FUNDINGS</span> ' .
                            Html::encode($model->title) . ' (' . Yii::t('XcoinModule.funding', 'Deleted Campaign') . ' )';
                    }
                    if ($model->account_type == Account::TYPE_DEFAULT) {
                        return '<span class="label label-info">DEFAULT</span>';
                    }
                    if ($model->account_type == Account::TYPE_TASK) {
                        return $model->task ? '<span class="label label-info">'.Yii::t('XcoinModule.funding','task').'</span> ' .
                            Html::a(
                                Html::encode($model->title),
                                [
                                    '/tasks/task/view',
                                    'id' => $model->task->id,
                                    'container' => $this->contentContainer
                                ]
                            ) : '<span class="label label-danger">TASK</span> ' .
                            Html::encode($model->title) . ' (' . Yii::t('XcoinModule.funding', 'Deleted Task') . ' )';
                    }
                    return Html::encode($model->title) . '<i class="fa fa-users>"></i>';
                }
            ],
            [
                'label' => Yii::t('XcoinModule.base', 'Asset(s) balance'),
                'format' => 'raw',
                'value' => function ($model) {
                    $assetName = isset($this->requireAsset) ? $this->requireAsset->getSpace()->one()->name : '';
                    if (
                        $assetName !== '' &&
                        (
                            !PurchaseCoin::isEnabled() ||
                            $assetName !== Yii::$app->params['coinPurchase']['space']
                        )
                    ) {
                        $assetName = '';
                    }

                    $list = [];
                    foreach ($model->getAssets() as $asset) {
                        if ($assetName === '' || $assetName === $asset->space->name)
                            $list[] = '<strong>' . $model->getAssetBalance($asset) . '</strong>&nbsp; ' .
                                SpaceImage::widget(['space' => $asset->space, 'width' => 20, 'showTooltip' => true, 'link' => true]);
                    }

                    return implode('&nbsp;&nbsp;&middot;&nbsp;&nbsp;', $list);
                }
            ],
            [
                'format' => 'raw',
                'options' => ['style' => 'width:55px'],
                'value' => function ($model) use ($contentContainer) {
                    $route = $this->nextRoute;
                    $route['accountId'] = $model->id;
                    if ($this->product) {
                        $route['productId'] = $this->product->id;
                    }

                    $disabled = $model->isEmpty();
                    if ($this->disableAccount !== null && $model->id == $this->disableAccount->id) {
                        $disabled = true;
                    }

                    if ($this->requireAsset && $model->getAssetBalance($this->requireAsset) == 0) {
                        $disabled = true;
                    }

                    return Html::a(Yii::t('XcoinModule.base', 'Select'), $route, ['class' => 'btn btn-sm btn-success', 'data-target' => '#globalModal', 'data-ui-loader' => '', 'disabled' => $disabled]);
                }
            ],
        ];

        parent::init();
    }

}
