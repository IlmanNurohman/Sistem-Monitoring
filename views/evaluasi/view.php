<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Evaluasi $model */

$this->title = 'Detail Evaluasi';
\yii\web\YiiAsset::register($this);
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
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        </p>
        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <div class="card-title">
                                <?= Html::encode($this->title) ?>
                            </div>
                        </div>
                        <p class="card-category">
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">
                                <div class="evaluasi-view">

                                    <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'label' => 'Nama Siswa',
            'value' => function ($model) {
                return $model->siswaKelas ? $model->siswaKelas->nama : '-';
            },
        ],
        'aspek',
        'nilai',
        'keterangan:ntext',
        'tanggal',
    ],
]) ?>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>