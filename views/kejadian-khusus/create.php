<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>


<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Pembelajaran</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="bi bi-exclamation-triangle"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="bi bi-chevron-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Kejadian Khusus</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <div class="card-title">Detail Pengajuan Izin</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">
                                <?php $form = ActiveForm::begin([
    'id' => 'kejadian-form',
]); ?>

                                <?= $form->field($model, 'tipe')->dropDownList([
    'ijin' => 'Izin Tidak Masuk',
    'izin sakit' => 'Izin Sakit',
], ['prompt'=>'Pilih Jenis']) ?>

                                <?= $form->field($model, 'keterangan')->textarea(['rows'=>4]) ?>

                                <div class="form-group text-end">
                                    <?= Html::submitButton('Kirim Pengajuan', ['class' => 'btn btn-primary']) ?>
                                </div>

                                <?php ActiveForm::end(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
$('#kejadian-form').on('beforeSubmit', function(e) {
    e.preventDefault(); // hentikan submit default

    Swal.fire({
        title: 'Konfirmasi',
        text: "Yakin ingin mengirim pengajuan?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // submit manual
            $.post($(this).attr('action'), $(this).serialize())
                .done(function(data){
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Pengajuan berhasil dikirim!'
                    }).then(() => {
                        window.location.href = data.redirect; // redirect dari controller
                    });
                })
                .fail(function(){
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Terjadi kesalahan saat menyimpan data.'
                    });
                });
        }
    });

    return false; // cegah submit tetap jalan
});
JS;
$this->registerJs($js);
?>