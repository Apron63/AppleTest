<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/** @var ActiveDataProvider $dataProvider */
/** @var int $timeBooster title */

$this->title = 'Яблоки повсюду!';
?>

<div class="site-index">

    <div class="well">
        <?= Html::a('Создать яблочко', ['apple/create'], ['class' => 'btn btn-success']) ?>
        <span>Ускорение: <?= $timeBooster ?> часов</span>
        <?= Html::a('Время + 1 час', ['apple/time-boost'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Сброс ускорения', ['apple/reset-boost'], ['class' => 'btn btn-warning']) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'created_at:datetime',
            'dropped_at:datetime',
            'appleColor',
            'appleStatus',
            'size:percent',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{drop} {eat}',
                'buttons' => [
                    'drop' => function($url, $model, $key) {
                        if ($model->canDrop) {
                            return Html::a('Сбросить', ['apple/drop', 'id' => $model->id]);
                        } else {
                            return '';
                        }
                    },
                    'eat' => function($url, $model, $key) {
                        if ($model->canEat) {
                            return '<div class="get-percent-wrapper">'
                                . Html::a('Съесть', ['apple/eat'], ['class' => 'get-percent', 'id' => $model->id])
                                . ' '
                                . Html::input('text', $model->id, 0, ['class' => 'get-percent-input'])
                                . '</div>'
                            ;
                        } else {
                            return '';
                        }
                    },
                ],
            ],
        ],
    ])
    ?>

</div>

<?php
    $this->registerJs('
        $(".get-percent").on("click", function(e) {
            e.stopImmediatePropagation();
            e.preventDefault();
            let elem = $(this).closest(".get-percent-wrapper");
            let percent = $(elem).find(".get-percent-input").val();
            let id = $(elem).find(".get-percent").attr("id");
            window.location = "/apple/eat?id=" + id + "&percent=" + percent;
        })
    ', View::POS_LOAD);
