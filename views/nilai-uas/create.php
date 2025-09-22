<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $model app\models\Nilai */
$this->title = 'Tambah Nilai UAS';
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
                    <a href="#">Nilai UAS</a>
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
                                    <?= Html::submitButton('Simpan', ['class' => 'btn btn-primary']) ?>
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