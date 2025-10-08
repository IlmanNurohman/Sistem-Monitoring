<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Evaluasi $model */
/** @var yii\widgets\ActiveForm $form */

use app\models\SiswaKelas;
use app\models\Kelas;

if (!isset($siswaList)) {
    $userId = Yii::$app->user->id ?? null;
    $kelas = Kelas::find()->where(['wali_guru_id' => $userId])->one();
    $siswaList = $kelas
        ? SiswaKelas::find()
            ->where(['kelas_id' => $kelas->id])
            ->select(['nama', 'id'])
            ->indexBy('id')
            ->column()
        : [];
}
?>

<div class="evaluasi-form">

    <?php $form = ActiveForm::begin(); ?>

    <!-- Pilihan siswa -->
    <?= $form->field($model, 'siswa_kelas_id')->dropDownList(
        $siswaList,
        ['prompt' => 'Pilih Siswa']
    ) ?>

    <!-- wali_guru_id otomatis (tidak perlu diinput manual oleh guru) -->
    <?= $form->field($model, 'wali_guru_id')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>

    <!-- aspek -->
    <?= $form->field($model, 'aspek')->textInput(['maxlength' => true]) ?>

    <!-- nilai -->
    <?= $form->field($model, 'nilai')->dropDownList(
        ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'],
        ['prompt' => 'Pilih Nilai']
    ) ?>

    <!-- keterangan -->
    <?= $form->field($model, 'keterangan')->textarea(['rows' => 4]) ?>

    <!-- tanggal (date picker + otomatis isi tanggal hari ini) -->
    <?= $form->field($model, 'tanggal')->input('date', [
        'value' => date('Y-m-d'), // isi otomatis tanggal hari ini
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Simpan', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>