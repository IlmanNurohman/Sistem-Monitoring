<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Tambah User';
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
                                <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

                                <?= $form->field($model, 'username')->textInput() ?>
                                <?= $form->field($model, 'email')->textInput() ?>
                                <?= $form->field($model, 'password_hash')->passwordInput() ?>
                                <?= $form->field($model, 'role')->dropDownList([
    'admin' => 'Admin',
    'siswa' => 'Siswa',
    'guru' => 'Guru',
    'kepsek' => 'Kepala Sekolah',
], ['id' => 'role-select'], ['prompt'=>'Pilih Role']) ?>

                                <div id="nisn-field" style="display:none;">
                                    <?= $form->field($model, 'nisn')->textInput(['maxlength' => true]) ?>
                                </div>
                                <?= $form->field($model, 'file_foto', [
    'template' => "{label}\n<div class=\"input-group\">{input}</div>\n{hint}\n{error}"
])->fileInput(['class' => 'form-control', 'accept' => '.jpg,.png, .jpeg']) ?>



                                <div class="form-group text-end">
                                    <?= Html::submitButton('Simpan', ['class' => 'btn btn-primary']) ?>
                                </div>

                                <?php ActiveForm::end(); ?>
                                <?php
$this->registerJs("
    $('#role-select').on('change', function() {
        if($(this).val() === 'siswa') {
            $('#nisn-field').show();
        } else {
            $('#nisn-field').hide();
        }
    }).trigger('change');
");
?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>