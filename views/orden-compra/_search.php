<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\ContactoEmpresaCliente;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenCompraSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orden-compra-search">

    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
    ]);
    ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-2">
                    <?= $form->field($model, 'cotizacion_uid') ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-4">
                    <?= $form->field($model, 'estado_oc')->dropDownList($model->estadoOption, ['prompt' => 'Seleccione']) ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'codigo_oc_externo') ?>
                </div>
                <div class="col-lg-4">
                    <?php echo $form->field($model, 'fecha_entrega')->widget('kartik\\datecontrol\\DateControl', ['type' => 'date']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <!-- <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?> -->
    </div>

    <?php ActiveForm::end(); ?>

</div>
