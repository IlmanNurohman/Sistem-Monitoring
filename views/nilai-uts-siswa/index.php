<?php 
use yii\helpers\Html; 
use yii\helpers\Url;
use yii\widgets\ActiveForm;


// Ambil semester dari GET request
$semester = Yii::$app->request->get('semester', 1); // default semester 1

// Ambil semua mapel sesuai semester
$query = \app\models\Nilai::find()
    ->select('mapel')
    ->distinct()
    ->where([
        'jenis_nilai' => 'uts',
        'user_id' => Yii::$app->user->id,
    ]);

if (!empty($selectedSemester)) {
    $query->andWhere(['semester' => $selectedSemester]);
}

$mapelList = $query->column();

$this->title = 'Nilai UTS Saya';

// Function konversi angka ke huruf mutu
function getHurufMutu($nilai) {
    if ($nilai >= 80 && $nilai <= 100) return 'A';
    if ($nilai >= 70) return 'B';
    if ($nilai >= 60) return 'C';
    if ($nilai >= 50) return 'D';
    if ($nilai >= 40) return 'E';
    return '-';
}
?>

<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Pembelajaran</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="bi bi-file-earmark-medical"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="bi bi-chevron-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Nilai UTS</a>
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
                                Nilai UTS
                            </div>

                            <div>
                                <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => Url::to(['index']),
        ]); ?>

                                <?= Html::dropDownList('semester', $semester, [
            1 => 'Semester 1',
            2 => 'Semester 2',
        ], [
            'class' => 'form-control',
            'prompt' => '-- Pilih Semester --',
            'onchange' => 'this.form.submit()'
        ]) ?>

                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-mb-12">
                                <div class="table-responsive" style="overflow-x:auto; white-space:nowrap;">

                                    <?php if (!empty($mapelList)): ?>
                                    <table id="multi-filter-select"
                                        class="table table-bordered table-striped text-center">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">Mata Pelajaran</th>
                                                <th colspan="2">Nilai</th>
                                            </tr>
                                            <tr>
                                                <th>Angka</th>
                                                <th>Huruf Mutu</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($mapelList as $mapel): ?>
                                            <?php
                                            // Query nilai per mapel + filter semester
                                            $nilaiQuery = \app\models\Nilai::find()
                                                ->where([
                                                    'user_id' => Yii::$app->user->id,
                                                    'mapel' => $mapel,
                                                    'jenis_nilai' => 'uts',
                                                ]);
                                            
                                            if (!empty($selectedSemester)) {
                                                $nilaiQuery->andWhere(['semester' => $selectedSemester]);
                                            }

                                            $nilaiModel = $nilaiQuery->orderBy(['tanggal' => SORT_DESC])->one();

                                            $nilaiAngka = $nilaiModel->nilai ?? 0;
                                            $hurufMutu = getHurufMutu($nilaiAngka);
                                        ?>
                                            <tr>
                                                <td><?= Html::encode($mapel) ?></td>
                                                <td><?= $nilaiAngka ?></td>
                                                <td><?= $hurufMutu ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php else: ?>
                                    <p><i>Belum ada nilai yang tersedia untuk semester ini.</i></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>