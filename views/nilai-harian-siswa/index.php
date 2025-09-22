<?php 
use yii\helpers\Html; 
use yii\helpers\Url;

// Ambil semua mapel yang punya nilai harian untuk siswa login
$mapelList = \app\models\Nilai::find()
    ->select('mapel')
    ->distinct()
    ->where([
        'jenis_nilai' => 'harian',
        'user_id' => Yii::$app->user->id,
    ])
    ->column();

$this->title = 'Nilai Harian Saya';
?>



<?php if (!empty($mapelList)): ?>
<?php
    // Ambil semua tanggal unik untuk siswa ini (gabungan semua mapel)
    $tanggalListAll = \app\models\Nilai::find()
        ->select('tanggal')
        ->distinct()
        ->where([
            'jenis_nilai' => 'harian',
            'user_id' => Yii::$app->user->id,
        ])
        ->orderBy('tanggal ASC')
        ->column();

    // Group per bulan
    $groupedTanggalAll = [];
    foreach ($tanggalListAll as $tanggal) {
        $bulanTahun = date('F Y', strtotime($tanggal));
        $groupedTanggalAll[$bulanTahun][] = $tanggal;
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
                    <a href="#">Nilai Harian</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <div class="card-title">Daftar Nilai Harian</div>
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
                                                <th rowspan="2" style="vertical-align:middle;text-align:center;">Mata
                                                    Pelajaran
                                                </th>
                                                <?php foreach ($groupedTanggalAll as $bulan => $listTanggal): ?>
                                                <th colspan="<?= count($listTanggal) ?>" style="text-align:center;">
                                                    Nilai Harian - <?= $bulan ?>
                                                </th>
                                                <?php endforeach; ?>
                                            </tr>
                                            <tr>
                                                <?php foreach ($groupedTanggalAll as $listTanggal): ?>
                                                <?php foreach ($listTanggal as $tanggal): ?>
                                                <th style="text-align:center;"><?= date('d', strtotime($tanggal)) ?>
                                                </th>
                                                <?php endforeach; ?>
                                                <?php endforeach; ?>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <?php foreach ($mapelList as $mapel): ?>
                                            <tr>
                                                <td><?= Html::encode($mapel) ?></td>
                                                <?php foreach ($tanggalListAll as $tanggal): ?>
                                                <td style="text-align:center;">
                                                    <?php 
                                $nilai = \app\models\Nilai::find()
                                    ->where([
                                        'user_id' => Yii::$app->user->id,
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
                                <?php else: ?>
                                <p><i>Belum ada nilai harian yang tersedia.</i></p>
                                <?php endif; ?>
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