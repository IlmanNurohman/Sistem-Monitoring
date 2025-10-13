<?php
use yii\helpers\Html;
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
                    <a href="#">Data Siswa</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <div class="card-title"> Data <?= Html::encode($model->nama_kelas) ?></div>
                        </div>
                        <p class="card-category">
                            Wali Kelas: <?= Html::encode($model->waliGuru->username) ?>
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">
                                <div class="table-responsive" style="overflow-x:auto; white-space:nowrap;">
                                    <?php if ($model->siswaKelas): ?>
                                    <table id="multi-filter-select"
                                        class="table table-bordered table-striped table-hover text-center ">
                                        <thead>
                                            <tr>
                                                <th>Nama</th>
                                                <th>Jenis Kelamin</th>
                                                <th>Tanggal Lahir</th>
                                                <th>Alamat</th>
                                                <th>NISN</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($model->siswaKelas as $s): ?>
                                            <tr>
                                                <td><?= Html::encode($s->nama) ?></td>
                                                <td><?= Html::encode($s->jk) ?></td>
                                                <td><?= Yii::$app->formatter->asDate($s->tanggal_lahir) ?></td>
                                                <td><?= Html::encode($s->alamat) ?></td>
                                                <td><?= Html::encode($s->nisn) ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <?php else: ?>
                                    <em>Belum ada siswa</em>
                                    <?php endif; ?>
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