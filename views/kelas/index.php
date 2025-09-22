<?php
use yii\helpers\Html;


/** @var $kelas app\models\Kelas[] */
$this->title = 'Manajemen Kelas';
?>
<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Manajemen</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="bi bi-layers"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="bi bi-chevron-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Kelas</a>
                </li>
            </ul>
        </div>
        <p>
            <?= Html::a('<i class="bi bi-plus-circle"></i> Tambah Kelas', ['create'], ['class' => 'btn btn-primary']) ?>
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
                                    <table id="multi-filter-select"
                                        class="table table-bordered table-striped table-hover text-center ">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Kelas</th>
                                                <th>Wali Kelas</th>
                                                <th>Jumlah Siswa</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($kelas as $i => $k): ?>
                                            <tr>
                                                <td><?= $i + 1 ?></td>
                                                <td><?= Html::encode($k->nama_kelas) ?></td>
                                                <td><?= $k->waliGuru ? Html::encode($k->waliGuru->username) : '-' ?>
                                                </td>
                                                <td><?= count($k->siswaKelas) ?></td>
                                                <td>
                                                    <?= Html::a('Lihat', ['view', 'id' => $k->id], ['class' => 'btn btn-info btn-sm']) ?>
                                                    <?= Html::a('Hapus', ['delete', 'id' => $k->id], [
                        'class' => 'btn btn-danger btn-sm',
                        'data' => [
                            'confirm' => 'Yakin hapus kelas ini?',
                            'method' => 'post',
                        ],
                    ]) ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>



                                    <?php foreach (Yii::$app->session->getAllFlashes() as $type => $message): ?>
                                    <div class="alert alert-<?= $type ?>"><?= $message ?></div>
                                    <?php endforeach; ?>
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