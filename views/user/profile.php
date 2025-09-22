<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Edit Profile';
$fotoUrl = $model->foto
    ? Yii::getAlias('@web') . '/uploads/' . $model->foto
    : Yii::getAlias('@web') . '/uploads/default.png'; // siapkan default.png
?>
<div class="container" style="padding-top: 20px;">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Profile</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="bi bi-person"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="bi bi-chevron-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Profile</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="  col-mb-12">
                <div class="card card-round">
                    <div class="card-header">
                        <div class="card-head-row card-tools-still-right">
                            <div class="card-title"><?= Html::encode($this->title) ?></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="  col-mb-12">

                                <?php $form = ActiveForm::begin([
    'id' => 'profile-form',
    'options' => ['enctype' => 'multipart/form-data']
]); ?>
                                <div class="row g-4">
                                    <!-- Kiri: Foto -->
                                    <div class="col-md-4 text-center">
                                        <div class="mb-3">
                                            <img src="<?= $fotoUrl ?>" width="180"
                                                style="border-radius:50%; border:2px solid #ddd; padding:5px; margin-bottom:12px;">
                                        </div>

                                        <!-- Ini udah otomatis kaya input text (label + box) -->
                                        <?= $form->field($model, 'file_foto')->fileInput(['class' => 'form-control']) ?>
                                    </div>

                                    <!-- Kanan: Biodata -->
                                    <div class="col-md-8">
                                        <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>
                                        <?= $form->field($model, 'email')->input('email') ?>
                                        <?= $form->field($model, 'nisn')->textInput() ?>
                                        <?= $form->field($model, 'new_password')->passwordInput(['placeholder' => 'Kosongkan jika tidak ingin ganti']) ?>

                                        <div class="form-group mt-2 text-end">
                                            <?= Html::submitButton('Simpan', ['class' => 'btn btn-primary']) ?>
                                        </div>
                                    </div>
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
$this->registerJs("
    $('#profile-form').on('beforeSubmit', function(e) {
        e.preventDefault(); // stop submit dulu

        Swal.fire({
            title: 'Apakah kamu yakin?',
            text: 'Profil akan diperbarui!',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Ya, simpan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit(); // submit form
            }
        });

        return false;
    });
");
?>
<?php
if (Yii::$app->session->hasFlash('success')) {
    $msg = Yii::$app->session->getFlash('success');
    $this->registerJs("
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: '$msg',
            timer: 2000,
            showConfirmButton: false
        });
    ");
}

if (Yii::$app->session->hasFlash('error')) {
    $msg = Yii::$app->session->getFlash('error');
    $this->registerJs("
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: '$msg'
        });
    ");
}
?>