<?php
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\User[] $users */

$this->title = 'Manajemen User';
?>

<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Manajemen</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="bi bi-person-vcard"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="bi bi-chevron-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Users</a>
                </li>
            </ul>
        </div>



        <p>
            <?= Html::a('<i class="bi bi-plus"></i>Tambah User', ['create'], ['class' => 'btn btn-primary']) ?>
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
                                                <th>No</th> <!-- Ganti ID jadi Nomor -->
                                                <th>Username</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Foto</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach ($users as $user): ?>
                                            <tr>
                                                <td><?= $no++ ?></td> <!-- Nomor urut -->
                                                <td><?= Html::encode($user->username) ?></td>
                                                <td><?= Html::encode($user->email) ?></td>
                                                <td><?= Html::encode($user->role) ?></td>
                                                <td>
                                                    <?php if ($user->foto): ?>
                                                    <img src="<?= Yii::getAlias('@web/uploads/' . $user->foto) ?>"
                                                        width="50">
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?= Html::a('Edit', ['update', 'id' => $user->id], ['class' => 'btn btn-primary btn-sm']) ?>
                                                    <?= Html::a('Hapus', ['delete', 'id' => $user->id], [
                    'class' => 'btn btn-danger btn-sm',
                    'data' => [
                        'confirm' => 'Yakin ingin menghapus user ini?',
                        'method' => 'post',
                    ],
                ]) ?>
                                                </td>
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