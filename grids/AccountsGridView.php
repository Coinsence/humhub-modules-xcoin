<?php

namespace humhub\modules\xcoin\grids;

use humhub\modules\xcoin\helpers\SpaceHelper;
use humhub\modules\xcoin\models\Funding;
use humhub\widgets\ModalConfirm;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Html;
use humhub\widgets\GridView;
use yii\data\ActiveDataProvider;
use humhub\modules\xcoin\models\Account;
use humhub\modules\space\widgets\Image as SpaceImage;
use humhub\modules\user\widgets\Image as UserImage;
use humhub\modules\xcoin\helpers\AccountHelper;
use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use yii\helpers\Url;

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
     * @throws InvalidConfigException
     */
    public $listAssetss;

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
                        return '<span class="label label-info">TASK</span> ' .
                            Html::a(
                                Html::encode($model->title),
                                [
                                    '/tasks/task/view',
                                    'id' => $model->task->id,
                                    'container' => $this->contentContainer
                                ]
                            );
                    }

                    return Html::encode($model->title) . '<i class="fa fa-users>"></i>';
                }
            ],
            [
                'label' => Yii::t('XcoinModule.base', 'Asset(s) balance'),
                'format' => 'raw',
                'value' => function ($model) {
                    $list = [];
                    foreach ($model->getAssets() as $asset) {
                        $list[] = '<div class="asset-balance"><strong>' . $model->getAssetBalance($asset) . '</strong>' .
                            SpaceImage::widget(['space' => $asset->space, 'width' => 20, 'showTooltip' => true, 'link' => true]) . '</div>';
                    }
                    return implode('', $list);
                }
            ],
            [
                'format' => 'raw',
                'options' => ['style' => 'width:220px;text-align:right'],
                'contentOptions' => ['style' => 'text-align:right'],
                'value' => function ($model) {

                    $transferButton = $loadPKButton = $disabledButton = '';

                    if (AccountHelper::canManageAccount($model) && Account::TYPE_TASK != $model->account_type) {
                        if (Account::TYPE_FUNDING == $model->account_type) {
                            // allow transfer only if investment is accepted
                            $funding = $model->getFunding()->one();
                            if ($funding && (in_array($funding->status, [Funding::FUNDING_STATUS_INVESTMENT_ACCEPTED, Funding::FUNDING_STATUS_INVESTMENT_RESTARTED]))) {
                                $transferButton = Html::a(
                                        '<i class="fa fa-exchange" aria-hidden="true"></i>',
                                        [
                                            '/xcoin/transaction/transfer',
                                            'accountId' => $model->id,
                                            'container' => $this->contentContainer
                                        ],
                                        [
                                            'class' => 'btn btn-default',
                                            'data-target' => '#globalModal'
                                        ]
                                    ) . '&nbsp;';
                            } else {
                                $transferButton = Html::a(
                                        '<i class="fa fa-exchange" aria-hidden="true"></i>',
                                        ['javascript:;'],
                                        [
                                            'class' => 'btn btn-default',
                                            'disabled' => true,
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'right',
                                            'title' => 'Transfer can be done only when investment is accepted for related crowdfunding campaign',
                                            'onclick' => 'return false;'
                                        ]
                                    ) . '&nbsp;';
                            }
                        } else {
                            // Module settings allowDirectCoinTransfer parameter value
                            $allowDirectCoinTransfer = SpaceHelper::allowDirectCoinTransfer($model);

                            if (!isset($allowDirectCoinTransfer) || $allowDirectCoinTransfer) {
                                $accountAssetsList = AccountHelper::getAssetsList($model);

                                if (!empty($accountAssetsList))
                                    $transferButton = Html::a(
                                            '<i class="fa fa-exchange" aria-hidden="true"></i>',
                                            [
                                                '/xcoin/transaction/transfer',
                                                'accountId' => $model->id,
                                                'container' => $this->contentContainer
                                            ],
                                            [
                                                'class' => 'btn btn-default',
                                                'data-target' => '#globalModal'
                                            ]
                                        ) . '&nbsp;';
                                else
                                    $transferButton = Html::a(
                                            '<i class="fa fa-exchange" aria-hidden="true"></i>',
                                            ['javascript:;'],
                                            [
                                                'class' => 'btn btn-default',
                                                'disabled' => true,
                                                'data-toggle' => 'tooltip',
                                                'data-placement' => 'right',
                                                'title' => 'No assets available on this account!',
                                                'onclick' => 'return false;'
                                            ]
                                        ) . '&nbsp;';
                            } else
                                $transferButton = Html::a(
                                        '<i class="fa fa-exchange" aria-hidden="true"></i>',
                                        ['javascript:;'],
                                        [
                                            'class' => 'btn btn-default',
                                            'disabled' => true,
                                            'data-toggle' => 'tooltip',
                                            'data-placement' => 'right',
                                            'title' => Yii::t('XcoinModule.base', 'Direct coin transfer disabled by the space admin'),
                                            'onclick' => 'return false;'
                                        ]
                                    ) . '&nbsp;';
                        }

                        // load private key button if user account
                        if ($this->contentContainer instanceof User) {
                            $loadPKButton = Html::a(
                                    '<i class="fa fa-key" aria-hidden="true"></i>',
                                    [
                                        '/xcoin/ethereum/load-private-key',
                                        'accountId' => $model->id,
                                        'container' => $this->contentContainer
                                    ],
                                    [
                                        'class' => 'btn btn-default',
                                        'data-target' => '#globalModal'
                                    ]
                                ) . '&nbsp;';
                        }
                    }

                    if ($model->space != null && AccountHelper::canManageAccount($model) && in_array($model->account_type, [Account::TYPE_STANDARD, Account::TYPE_COMMUNITY_INVESTOR, Account::TYPE_FUNDING])) {
                        if ($model->account_type == Account::TYPE_FUNDING) {
                            if (!$model->funding) {
                                $disabledButton = ModalConfirm::widget([
                                    'uniqueID' => 'model_disable_account' . $model->id,
                                    'title' => Yii::t('XcoinModule.base', '<strong>Confirm</strong> disabling account'),
                                    'message' => Yii::t('XcoinModule.base', 'When disabling account, all COINs will be transferred to space default account.'),
                                    'buttonTrue' => Yii::t('XcoinModule.base', 'Disable'),
                                    'buttonFalse' => Yii::t('XcoinModule.base', 'Cancel'),
                                    'linkContent' => '<i class="fa fa-ban"></i> ',
                                    'cssClass' => 'btn btn-default',
                                    'linkHref' => Url::to(['/xcoin/account/disable', 'id' => $model->id, 'container' => $this->contentContainer])
                                ]);
                            }
                        } else {
                            $disabledButton = ModalConfirm::widget([
                                'uniqueID' => 'model_disable_account' . $model->id,
                                'title' => Yii::t('XcoinModule.base', '<strong>Confirm</strong> disabling account'),
                                'message' => Yii::t('XcoinModule.base', 'Do you really want to disable this account?'),
                                'buttonTrue' => Yii::t('XcoinModule.base', 'Disable'),
                                'buttonFalse' => Yii::t('XcoinModule.base', 'Cancel'),
                                'linkContent' => '<i class="fa fa-ban"></i> ',
                                'cssClass' => 'btn btn-default',
                                'linkHref' => Url::to(['/xcoin/account/disable', 'id' => $model->id, 'container' => $this->contentContainer])
                            ]);
                        }
                    }

                    $overviewButton = Html::a('<i class="fa fa-search" aria-hidden="true"></i>', ['/xcoin/account', 'id' => $model->id, 'container' => $this->contentContainer], ['class' => 'btn btn-default']);

                    return $loadPKButton . $transferButton . $disabledButton . $overviewButton;
                }
                ],
            ];

        parent::init();

    }
}
