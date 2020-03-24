<?php

use humhub\modules\xcoin\assets\Assets;
use humhub\modules\xcoin\models\Funding;
use kv4nt\owlcarousel\OwlCarouselWidget;

Assets::register($this);

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
                        <span>All</span>
                    </label>
                    <?php foreach ($categories as $category): ?>
                        <label class="category">
                            <input type="radio" name="categroy" value="<?= $category['id'] ?>">
                            <span style="background-image: url('<?= $category['img'] ?>'); "><?= $category['text'] ?></span>
                        </label>
                    <?php endforeach; ?>
                    <?php OwlCarouselWidget::end(); ?>
                </div>
            </div>
            <div class="row"></div>
        </div>
    </div>
    <div class="content">
        <div class="row container">

        </div>
    </div>
</div>
