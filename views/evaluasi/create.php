<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Evaluasi $model */

$this->title = 'Create Evaluasi';
$this->params['breadcrumbs'][] = ['label' => 'Evaluasis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="evaluasi-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
