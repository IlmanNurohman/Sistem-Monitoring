<?php 
use yii\helpers\Html; 
use yii\widgets\ActiveForm;
use yii\helpers\Url;

// Ambil daftar mapel (hardcode atau dari DB)
$mapelOptions = [
    'Matematika' => 'Matematika',
    'B.Indonesia' => 'Bahasa Indonesia',
     'B.Sunda' => 'Bahasa Sunda',
      'B.Inggris' => 'Bahasa Inggris',
    'PABP' => 'PABP',
    'IPAS' => 'IPAS',
    'P.Pancasila' => 'P.Pancasila',
    'PLH' => 'PLH',
    'PJOK' => 'PJOK',
    'SBDP' => 'SBDP',
];

// Ambil daftar tanggal sesuai mapel yang dipilih
$tanggalListMapel = [];
$groupedTanggalMapel = [];
if (!empty($mapel)) {
    $tanggalListMapel = \app\models\Nilai::find()
        ->select('tanggal')
        ->distinct()
        ->where([
            'jenis_nilai' => 'harian',
            'mapel' => $mapel,
        ])
        ->orderBy('tanggal ASC')
        ->column();

    foreach ($tanggalListMapel as $tanggal) {
        $bulanTahun = date('F Y', strtotime($tanggal));
        $groupedTanggalMapel[$bulanTahun][] = $tanggal;
    }
}




$this->title = 'Daftar Nilai Harian';
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
                    <a href="#">Nilai Harian</a>
                </li>
            </ul>
        </div>

        <p><?= Html::a('<i class="bi bi-plus-circle"></i> Input Nilai Harian', ['create'], ['class' => 'btn btn-primary']) ?>
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

                                <?= Html::dropDownList('mapel', $mapelOptions, [
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
], [
            'class' => 'form-control',
            'prompt' => '-- Pilih Mata Pelajaran --',
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
                                                <th rowspan="2" style="vertical-align:middle;text-align:center;">No</th>
                                                <th rowspan="2" style="vertical-align:middle;text-align:center;">Siswa
                                                </th>
                                                <th rowspan="2" style="vertical-align:middle;text-align:center;">Mata
                                                    Pelajaran</th>

                                                <?php foreach ($groupedTanggalMapel as $bulan => $listTanggal): ?>
                                                <th colspan="<?= count($listTanggal) ?>" style="text-align:center;">
                                                    Nilai Harian - <?= $bulan ?>
                                                </th>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <?php foreach ($groupedTanggalMapel as $listTanggal): ?>
                                                <?php foreach ($listTanggal as $tanggal): ?>
                                                <th style="text-align:center;"><?= date('d', strtotime($tanggal)) ?>
                                                </th>
                                                <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($siswaList as $index => $siswa): ?>
                                            <tr>
                                                <td style="text-align:center;"><?= $index+1 ?></td>
                                                <td><?= $siswa->nama ?? '-' ?></td>
                                                <td><?= $mapel ?? '-' ?></td>

                                                <?php foreach ($tanggalListMapel as $tanggal): ?>
                                                <td style="text-align:center;">
                                                    <?php 
                    $nilai = \app\models\Nilai::find()
                        ->where([
                            'user_id' => $siswa->user_id,
                            'mapel' => $mapel,
                            'tanggal' => $tanggal,
                            'jenis_nilai' => 'harian'
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