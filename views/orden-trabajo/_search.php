<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenTrabajoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orden-trabajo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'identificador') ?>
    <?= $form->field($model, 'fecha_ingreso')->widget('kartik\\datecontrol\\DateControl', [
    'type' => 'date',
]) ?>
    <?= $form->field($model, 'fecha_inicio')->widget('kartik\\datecontrol\\DateControl', [
    'type' => 'date',
]) ?>


    <?php /* echo $form->field($model, 'estado_cumplimiento') */ ?>
    <?php /* echo $form->field($model, 'user_id') */ ?>
    <?php /* echo $form->field($model, 'orden_compra_id') */ ?>
    <?php /* echo $form->field($model, 'item_id') */ ?>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <!-- <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?> -->
    </div>

    <?php ActiveForm::end(); ?>

</div>
