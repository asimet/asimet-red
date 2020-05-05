<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OfertaSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="oferta-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, '_id') ?>
    <?= $form->field($model, 'fecha_cierre')->widget('kartik\\datecontrol\\DateControl', [
    'type' => 'date',
]) ?>
    <?= $form->field($model, 'fecha_entrega')->widget('kartik\\datecontrol\\DateControl', [
    'type' => 'date',
]) ?>
    <?= $form->field($model, 'estado') ?>
    <?= $form->field($model, 'detalle') ?>
    <?php /* echo $form->field($model, 'uuid') */ ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <!-- <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?> -->
    </div>

    <?php ActiveForm::end(); ?>

</div>
