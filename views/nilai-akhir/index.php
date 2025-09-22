<?php 
use yii\helpers\Html; 
use yii\grid\GridView; 
use yii\widgets\ActiveForm;
use yii\helpers\Url;



  $semester = Yii::$app->request->get('semester', 1); // default semester 1
// Ambil daftar tanggal unik dari dataProvider
$tanggalList = \app\models\Nilai::find()
    ->select('tanggal')
    ->distinct()
    ->where(['jenis_nilai' => 'akhir'])
    ->orderBy('tanggal ASC')
    ->column();

    $mapelOptions = [
    'B.Indonesia' => 'Bahasa Indonesia',
      'B.Inggris' => 'Bahasa Inggris',
       'B.Sunda' => 'Bahasa Sunda',
       'IPAS' => 'IPAS',
       'Matematika' => 'Matematika',
    'PABP' => 'PABP',
    'P.Pancasila' => 'P.Pancasila',
    'PLH' => 'PLH',
    'PJOK' => 'PJOK',
    'SBDP' => 'SBDP',
];

$mapelList = \app\models\Nilai::find()
    ->select('mapel')
    ->distinct()
    ->where([
        'jenis_nilai' => 'akhir',
        'semester' => $semester, // 🔥 filter semester
    ])
    ->orderBy('mapel ASC')
    ->column();


// Ambil daftar siswa unik
$siswaList = \app\models\SiswaKelas::find()
    ->with('user')
    ->where(['user_id' => $siswaIds]) // ✅ hanya siswa kelas wali guru
    ->all();


    $rankingData = [];
foreach ($siswaList as $siswa) {
    $totalNilai = 0;
    foreach ($mapelList as $mapelName) {
        $nilai = \app\models\Nilai::find()
            ->where([
                'user_id' => $siswa->user_id,
                'mapel' => $mapelName,
                'jenis_nilai' => 'akhir',
                'semester' => $semester,
            ])
            ->one();
        if ($nilai && $nilai->nilai !== null) {
            $totalNilai += $nilai->nilai;
        }
    }
    $rankingData[$siswa->user_id] = [
        'siswa' => $siswa,
        'total' => $totalNilai
    ];
}

// 🔥 Urutkan berdasarkan total nilai DESC
usort($rankingData, function($a, $b) {
    return $b['total'] <=> $a['total'];
});

// 🔥 Assign peringkat
$rank = 1;
foreach ($rankingData as &$data) {
    if ($rank <= 10 && $data['total'] > 0) {
        $data['rank'] = $rank++;
    } else {
        $data['rank'] = '-';
    }
}
unset($data);

// 🔥 Convert ke array indexed ulang
$rankingData = array_values($rankingData);
?>


<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Sekolah</h3>
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
                    <a href="#">Nilai Akhir</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-mb-12">
                <p> <?= Html::a('<i class="bi bi-plus-circle"></i> Input Nilai Akhir', ['create'], ['class' => 'btn btn-primary']) ?>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div
                            class="card-head-row card-tools-still-right d-flex justify-content-between align-items-center">
                            <div class="card-title">
                                Daftar Nilai Akhir Siswa <?= Html::encode($model->nama_kelas) ?>
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
                            <div class="  col-mb-12">
                                <div class="table-responsive" style="overflow-x:auto; white-space:nowrap;">
                                    <table id="multi-filter-select"
                                        class="table table-bordered table-striped table-hover text-center ">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">No</th>
                                                <th rowspan="2">Siswa</th>
                                                <th colspan="<?= count($mapelList) ?>">Nilai Akhir</th>
                                                <th rowspan="2">Total</th>
                                                <th rowspan="2">Peringkat</th> <!-- ✅ kolom peringkat -->
                                            </tr>
                                            <tr>
                                                <?php foreach ($mapelList as $mapelName): ?>
                                                <th><?= $mapelName ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($rankingData as $index => $data): ?>
                                            <?php $siswa = $data['siswa']; ?>
                                            <tr>
                                                <td><?= $index+1 ?></td>
                                                <td><?= $siswa->nama ?? '-' ?></td>

                                                <?php foreach ($mapelList as $mapelName): 
                    $nilai = \app\models\Nilai::find()
                        ->where([
                            'user_id' => $siswa->user_id,
                            'mapel' => $mapelName,
                            'jenis_nilai' => 'akhir',
                            'semester' => $semester,
                        ])
                        ->one();
                ?>
                                                <td><?= $nilai->nilai ?? '-' ?></td>
                                                <?php endforeach; ?>

                                                <td><b><?= $data['total'] > 0 ? $data['total'] : '-' ?></b></td>
                                                <td style="color:blue; font-weight:bold;"><?= $data['rank'] ?></td>
                                                <!-- ✅ tampilkan peringkat -->
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
$this->registerJs("
    $(document).ready(function() {
        $('#basic-datatables').DataTable({});

        $('#multi-filter-select').DataTable({
            pageLength: 10,
            initComplete: function() {
                this.api()
                    .columns()
                    .every(function() {
                        var column = this;
                        var select = $('<select class=\"form-select\"><option value=\"\"></option></select>')
                            .appendTo($(column.footer()).empty())
                            .on('change', function() {
                                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                column.search(val ? '^'+val+'$' : '', true, false).draw();
                            });

                        column.data().unique().sort().each(function(d, j) {
                            select.append('<option value=\"'+d+'\">'+d+'</option>');
                        });
                    });
            }
        });

        $('#add-row').DataTable({ pageLength: 5 });

        var action = '<td> <div class=\"form-button-action\"> <button type=\"button\" class=\"btn btn-link btn-primary btn-lg\"> <i class=\"fa fa-edit\"></i> </button> <button type=\"button\" class=\"btn btn-link btn-danger\"> <i class=\"fa fa-times\"></i> </button> </div> </td>';

        $('#addRowButton').click(function() {
            $('#add-row').dataTable().fnAddData([
                $('#addName').val(),
                $('#addPosition').val(),
                $('#addOffice').val(),
                action,
            ]);
            $('#addRowModal').modal('hide');
        });
    });
");
?>