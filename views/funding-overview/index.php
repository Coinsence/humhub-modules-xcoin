<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\widgets\ActiveForm;
use kv4nt\owlcarousel\OwlCarouselWidget;
use humhub\assets\Select2BootstrapAsset;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use \yii\helpers\Url;
use \yii\helpers\Html;


/** @var $spacesList array */
/** @var $challengesList array */
/** @var $countriesList array */

Assets::register($this);
Select2BootstrapAsset::register($this);


$img_placeholder = 'https://via.placeholder.com/600x400.png';
$categories = [
    [
        'id'    => 1,
        'text'  => 'Arts and Culture',
        'img'   => $img_placeholder
    ],
    [
        'id'    => 2,
        'text'  => 'Quality education and awareness creation',
        'img'   => $img_placeholder
    ],
    [
        'id'    => 3,
        'text'  => 'Child protection and youth empowerment',
        'img'   => $img_placeholder
    ],
    [
        'id'    => 4,
        'text'  => 'Community building and cohesion',
        'img'   => $img_placeholder
    ],
];

?>

<div class="crowd-funding">
    <div class="filters">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    OwlCarouselWidget::begin([
                        'container' => 'div',
                        'containerOptions' => [
                            'class' => 'categories'
                        ],
                        'pluginOptions'     => [
                            'responsive' => [
                                0       => [
                                    'items' => 2
                                ],
                                520     => [
                                    'items' => 3
                                ],
                                768     => [
                                    'items' => 4
                                ],
                                1192    => [
                                    'items' => 5
                                ],
                                1366    => [
                                    'items' => 6
                                ],
                                1556    => [
                                    'items' => 8
                                ],
                            ],
                            'margin'        => 10,
                            'nav'           => true,
                            'dots'          => false,
                            'navText'       => ['<i class="fa fa-chevron-left"></i>', '<i class="fa fa-chevron-right"></i>']
                        ]
                    ]);
                    ?>
                    <label class="category all">
                        <input type="radio" name="categroy" checked>
                        <a href="<?= Url::to(['/xcoin/funding-overview']) ?>"><span>All</span></a>
                    </label>
                    <?php foreach ($categories as $category): ?>
                        <label class="category">
                            <input type="radio" name="categroy" value="<?= $category['id'] ?>">
                            <a href="<?= Url::to(['/xcoin/funding-overview', 'category' => $category['id']]) ?>"><span style="background-image: url('<?= $category['img'] ?>'); "><?= $category['text'] ?></span></a>
                        </label>
                    <?php endforeach; ?>
                    <?php OwlCarouselWidget::end(); ?>
                </div>
            </div>

            <?php $form = ActiveForm::begin(['id' => 'filter-form']); ?>
            <div class="row">
                <div class="col-md-3 space">
                    <?=
                    $form->field($model, 'space_id')->widget(Select2::class, [
                        'data' => $spacesList,
                        'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Space') . ' - ', 'value' => []],
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                        ],
                    ])->label(false)
                    ?>
                </div>
                <div class="col-md-3 challenge">
                    <?=
                    $form->field($model, 'challenge_id')->widget(Select2::class, [
                        'data' => $challengesList,
                        'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Challenge') . ' - ', 'value' => []],
                        'theme' => Select2::THEME_BOOTSTRAP,
                        'hideSearch' => true,
                        'pluginOptions' => [
                            'allowClear' => true,
                            'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                        ],
                    ])->label(false)
                    ?>
                </div>
                <div class="col-md-3 location">
                    <div id="location-field" class="location-field">
                        <div class="location-selection">
                            <span class="selection-text">test, tt</span>
                            <span class="selection-arrow">
                                <b></b>
                            </span>
                        </div>
                        <div class="location-dropdown">
                            <div class="dropdown-body">
                                <div class="row">
                                    <div class="col-md-6"><?= Yii::t('XcoinModule.funding', 'Country:') ?></div>
                                    <div class="col-md-6">
                                        <?=
                                        $form->field($model, 'country')->widget(Select2::class, [
                                            'data' => $countriesList,
                                            'options' => ['placeholder' => '- ' . Yii::t('XcoinModule.funding', 'Select a Country') . ' - ', 'value' => []],
                                            'theme' => Select2::THEME_BOOTSTRAP,
                                            'hideSearch' => true,
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'escapeMarkup' => new JsExpression("function(m) { return m; }"),
                                            ],
                                        ])->label(false)
                                        ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6"><?= Yii::t('XcoinModule.funding', 'City:') ?></div>
                                    <div class="col-md-6">
                                        <?=
                                        $form->field($model, 'city')->textInput([
                                            'placeholder' => Yii::t('XcoinModule.funding', 'Type your city name')
                                        ])->label(false)
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-footer">
                                <a class="reset-location" href="javascript:;"><i class="fa fa-undo"></i> Reset</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 keywords">
                    <?=
                    $form->field($model, 'keywords')->textInput([
                        'placeholder' => Yii::t('XcoinModule.funding', 'Search by keyword..')
                    ])->label(false)
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 filter-actions">
                    <?= Html::submitButton(Yii::t('XcoinModule.funding', 'Apply filter'), ['class' => 'sumbit btn btn-gradient-1']) ?>
                    <?= Html::resetButton(Yii::t('XcoinModule.funding', 'Reset'), ['class' => 'reset btn btn-default']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>
    <div class="content">
        <div class="row container">

        </div>
    </div>
</div>
