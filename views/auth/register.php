<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Register Siswa';
?>

<h1>Register Siswa</h1>

<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
<?= $form->field($model, 'email')->input('email') ?>
<?= $form->field($model, 'password')->passwordInput() ?>
<?= $form->field($model, 'file_foto')->fileInput() ?>

<div class="form-group">
    <?= Html::submitButton('Register', ['class' => 'btn btn-success']) ?>
</div>

<?php ActiveForm::end(); ?>