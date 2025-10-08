<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Evaluasi[] $evaluasi */
/** @var app\models\SiswaKelas $siswa */

$this->title = 'Hasil Evaluasi Pembelajaran';
?>

<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <h1><?= Html::encode($this->title) ?></h1>
        <h4>Nama: <?= Html::encode($siswa->nama) ?></h4>

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