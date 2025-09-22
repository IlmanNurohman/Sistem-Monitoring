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
    ->where(['jenis_nilai' => 'uas'])
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
        'jenis_nilai' => 'uas',
        'semester' => $semester, // 🔥 filter semester
    ])
    ->orderBy('mapel ASC')
    ->column();


// Ambil daftar siswa unik
$siswaList = \app\models\SiswaKelas::find()
    ->with('user')
    ->where(['user_id' => $siswaIds]) // ✅ hanya siswa kelas wali guru
    ->all();



    
$this->title = 'Daftar Nilai UAS'; ?>

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
                    <a href="#">Nilai UAS</a>
                </li>
            </ul>
        </div>
        <p> <?= Html::a('<i class="bi bi-plus-circle"></i> Input Nilai UAS', ['create'], ['class' => 'btn btn-primary']) ?>
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
                                                <th rowspan="2" style="vertical-align:middle;text-align:center;">No
                                                </th>
                                                <th rowspan="2" style="vertical-align:middle;text-align:center;">
                                                    Siswa
                                                </th>
                                                <th colspan="<?= count($mapelList) ?>" style="text-align:center;">
                                                    Nilai
                                                    UTS</th>
                                            </tr>
                                            <tr>
                                                <?php foreach ($mapelList as $mapelName): ?>
                                                <th style="text-align:center;"><?= $mapelName ?></th>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($siswaList as $index => $siswa): ?>
                                            <tr>
                                                <td style="text-align:center;"><?= $index+1 ?></td>
                                                <td><?= $siswa->nama ?? '-' ?></td>

                                                <?php foreach ($mapelList as $mapelName): ?>
                                                <td style="text-align:center;">
                                                    <?php 
                           $nilai = \app\models\Nilai::find()
    ->where([
        'user_id' => $siswa->user_id,
        'mapel' => $mapelName,
        'jenis_nilai' => 'uas',
        'semester' => $semester, // 🔥 filter semester
    ])
    ->one();

                            echo $nilai->nilai ?? '-';
                        ?>
                                                </td>
                                                <?php endforeach; ?>
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
    </div>
</div><?php
$this->registerJs("
    $(document).ready(function() {
        $('#basic-datatables').DataTable({});

        $('#multi-filter-select').DataTable({
            pageLength: 5,
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