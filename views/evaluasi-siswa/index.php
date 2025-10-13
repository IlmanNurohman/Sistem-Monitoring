<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Evaluasi[] $evaluasi */
/** @var app\models\SiswaKelas $siswa */

$this->title = 'Hasil Evaluasi Pembelajaran';
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
                        <p class="card-category">
                            Nama: <?= Html::encode($siswa->nama) ?>
                        </p>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">
                                <div class="table-responsive" style="overflow-x:auto; white-space:nowrap;">

                                    <?php if (!empty($evaluasi)): ?>
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Aspek</th>
                                                <th>Nilai</th>
                                                <th>Keterangan</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($evaluasi as $i => $item): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= Html::encode($item->aspek) ?></td>
                                                <td><?= Html::encode($item->nilai) ?></td>
                                                <td><?= Html::encode($item->keterangan) ?></td>
                                                <td><?= Yii::$app->formatter->asDate($item->tanggal) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        Belum ada data evaluasi untuk saat ini.
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>