<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

$this->title = 'Login';
?>

<div class="container" style="padding-top: 40px; max-width: 500px;">
    <div class="card shadow-lg rounded">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0 text-center">Login</h3>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'username') ?>
            <?= $form->field($model, 'password')->passwordInput() ?>

            <div class="form-group text-end">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$js = '';
if (Yii::$app->session->hasFlash('loginSuccess')) {
    $msg = Yii::$app->session->getFlash('loginSuccess');
    $js = "Swal.fire('Berhasil', '$msg', 'success');";
}
if (Yii::$app->session->hasFlash('loginError')) {
    $msg = Yii::$app->session->getFlash('loginError');
    $js = "Swal.fire('Gagal', '$msg', 'error');";
}
$this->registerJs($js, View::POS_END);
?>