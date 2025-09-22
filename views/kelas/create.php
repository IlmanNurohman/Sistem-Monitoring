<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\User;

$form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); 
$this->title = 'Tambah Kelas';
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

                                <?= $form->field($model, 'nama_kelas')->dropDownList([
    'Kelas 1' => 'Kelas 1',
    'Kelas 2' => 'Kelas 2',
    'Kelas 3' => 'Kelas 3',
    'Kelas 4' => 'Kelas 4',
    'Kelas 5' => 'Kelas 5',
    'Kelas 6' => 'Kelas 6',
], ['prompt' => 'Pilih Kelas']) ?>

                                <?= $form->field($model, 'wali_guru_id')->dropDownList(
    ArrayHelper::map(User::find()->where(['role' => 'guru'])->all(), 'id', 'username'),
    ['prompt' => 'Pilih Wali Guru']
) ?>

                                <?= $form->field($uploadModel, 'file_excel', [
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