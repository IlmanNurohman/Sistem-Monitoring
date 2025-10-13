<?php

use app\models\Evaluasi;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\EvaluasiSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Evaluasi';

?>
<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Sekolah</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="bi bi-mortarboard"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="bi bi-chevron-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Evaluasi</a>
                </li>
            </ul>
        </div>
        <p>
            <?= Html::a('Create Evaluasi', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div
                            class="card-head-row card-tools-still-right d-flex justify-content-between align-items-center">
                            <div class="card-title">
                                <?= Html::encode($this->title) ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">
                                <div class="table-responsive" style="overflow-x:auto; white-space:nowrap;">
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
            'header' => 'Aksi',
            'urlCreator' => function ($action, Evaluasi $model, $key, $index, $column) {
                return Url::toRoute([$action, 'id' => $model->id]);
            }
        ],
    ],
]); ?>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>