<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\models\Nilai */
$this->title = 'Tambah Nilai Harian';
$this->registerJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
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

                                <?php $form = ActiveForm::begin(['id' => 'nilai-form','options' => ['enctype' => 'multipart/form-data']]); ?>

                                <?= $form->field($model, 'mapel')->dropDownList([
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
                                ],['prompt' => 'Pilih Mata Pelajaran']) ?>

                                <?= $form->field($model, 'tanggal')->input('date') ?>

                                <?= $form->field($model, 'semester')->dropDownList([
    1 => 'Semester 1',
    2 => 'Semester 2',
], ['prompt' => 'Pilih Semester']) ?>

                                <!-- 🔥 ini jadi sama formatnya -->
                                <?= $form->field($model, 'excelFile', [
    'template' => "{label}\n<div class=\"input-group\">{input}</div>\n{hint}\n{error}"
])->fileInput(['class' => 'form-control', 'accept' => '.xlsx,.xls']) ?>

                                <div class="form-group text-end">
                                    <?= Html::submitButton('Simpan', ['class' => 'btn btn-primary','id' => 'submit-btn']) ?>
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
$('#nilai-form').on('beforeSubmit', function(e) {
    e.preventDefault(); 
    var form = $(this);

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data nilai akan disimpan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, simpan!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if(result.isConfirmed){
            var formData = new FormData(form[0]);
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response){
    if(response.status === 'success'){
        Swal.fire({
            title: 'Berhasil!',
            html: response.message, // pakai html kalau ada <br>
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = 'index'; // redirect ke index
            }
        });
        form[0].reset(); // opsional, reset form
    } else {
        Swal.fire('Gagal!', response.message, 'error');
    }
}
            });
        }
    });
    return false;
});
JS;
$this->registerJs($js);
?>