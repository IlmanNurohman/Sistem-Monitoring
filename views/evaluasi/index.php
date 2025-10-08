<?php

use app\models\Evaluasi;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\EvaluasiSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Evaluasis';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evaluasi-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Evaluasi', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
    'dataProvider' => $dataProvider,
   //'filterModel' => $searchModel,//
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'header' => 'No',
        ],

        [
            'attribute' => 'siswa_kelas_id',
            'label' => 'Nama Siswa',
            'value' => function ($model) {
                return $model->siswaKelas ? $model->siswaKelas->nama : '-';
            },
        ],
        'aspek',
        'nilai',
        //'keterangan:ntext',
        //'tanggal',

        [
            'class' => ActionColumn::className(),
            'urlCreator' => function ($action, Evaluasi $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->id]);
            }
        ],
    ],
]); ?>


</div>