<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use asimet\red\models\Cotizacion;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenTrabajo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orden-trabajo-form">

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => "off"]]); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-4">
                    <?php if ($model->isNewRecord): ?>
                        <?= $form->field($model, 'fecha_ingreso')->widget('kartik\\datecontrol\\DateControl', ['type' => 'date']) ?>
                    <?php else : ?>
                        <?= $model->getAttributeLabel('fecha_ingreso') . ' : ' . $model->fecha_ingreso ?>
                    <?php endif ?>
                </div>
                <div class="col-lg-4">
                    <?php echo $form->field($model, 'fecha_inicio')->widget('kartik\\datecontrol\\DateControl', ['type' => 'date']); ?>
                </div>
                <div class="col-lg-4">
                    <?= $form->field($model, 'fecha_entrega')->widget('kartik\\datecontrol\\DateControl', ['type' => 'date']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <?php if ($model->isNewRecord): ?>
                    <div class="col-lg-3">
                        <?=
                        $form->field($model, 'cotizacion_uid')->widget(Select2::classname(), [
                            'data' => Cotizacion::getOptionList(),
                            'options' => ['placeholder' => 'Selecciona cotización ...'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-lg-3">
                        <?=
                        $form->field($model, 'item')->widget(DepDrop::classname(), [
                            'pluginOptions' => [
                                'data' => ($model->isNewRecord) ? [] : $model->cotizacion->itemOption,
                                'depends' => ['ordentrabajo-cotizacion_uid'],
                                'placeholder' => 'Select...',
                                'url' => Url::to(['/ared/orden-trabajo/item'])
                            ]
                        ]);
                        ?>

                    </div>

                    <div class="col-lg-3">
                        <?php 
                        /* echo
                        $form->field($model, 'orden_compra_id')->widget(Select2::classname(), [
                            'data' => OrdenCompra::getOptionList(),
                            'options' => ['placeholder' => 'Selecciona ...'],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]);*/
                        ?>
                    </div>
                <?php else : ?>

                    <div class="col-lg-3">
                        <?= $model->getAttributeLabel('cliente_uuid') . ' : ' ?>
                    </div>
                    <div class="col-lg-3">
                        <?= $model->getAttributeLabel('cotizacion_uid') . ' : ' ?>
                    </div>
                    <div class="col-lg-3">
                        <?= 'Item seleccionado : ' ?>
                    </div>

                <?php endif ?>

            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
