<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use asimet\red\models\Cotizacion;
use yii\helpers\Url;
use kartik\file\FileInput;
use yii\widgets\Pjax;
use kartik\switchinput\SwitchInput;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenCompra */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="orden-compra-form">

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => "off"]]); ?>

    <div class="row">
        <div class="col-lg-3">
            <?= $form->field($model, 'codigo_oc_externo')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
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
        <div class="col-lg-4">
            <?php
            echo $form->field($model, 'allItem')->widget(SwitchInput::classname(), [
                'pluginOptions' => [
                    'onText' => 'Si',
                    'offText' => 'No',
                    'onColor' => 'success',
                    'offColor' => 'danger',
                ],
                'pluginEvents' => [
                    "switchChange.bootstrapSwitch" => "function() { if($(this).prop('checked')) { $('#div-items').hide(); } else { $('#div-items').show(); } }",
                ],
            ]);
            ?>
        </div>
    </div>

    <div class="row" id="div-items" style="<?= (!$model->isNewRecord && $model->allItem) ? 'display: none' : '' ?> ">
        <div class="col-lg-5">
            <?php echo Html::hiddenInput('selected_item_id', $model->isNewRecord ? '' : $model->item, ['id' => 'selected_item_id']); ?>
            <?=
            $form->field($model, 'item')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'data' => ($model->isNewRecord) ? [] : $model->cotizacion->getItemOptionList([Cotizacion::ESTADO_ITEM_ACEPTADO]),
                    'initialize' => $model->isNewRecord ? false : true,
                    'depends' => ['ordencompra-cotizacion_uid'],
                    'placeholder' => 'Select...',
                    'url' => Url::to(['/ared/orden-compra/item']),
                    'params' => ['selected_item_id'],
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-3">
                    <?=
                    $form->field($model, 'fecha_entrega')->widget('kartik\\datecontrol\\DateControl', [
                        'type' => 'date',
                    ])
                    ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'dias_previo_aviso_entrega')->textInput() ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'estado_oc')->dropDownList($model->estadoOption, ['prompt' => 'Seleccione']) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-3">
                    <?=
                    $form->field($model, 'file_orden')->widget(FileInput::classname(), [
                        'pluginOptions' => [
                            'initialPreview' => empty($model->_id) ? false : $model->linkOrden,
                            'showUpload' => false,
                            'initialPreviewAsData' => !empty($model->_id),
                            'initialPreviewConfig' => empty($model->_id) ? false : [['type' => 'pdf', 'url' => Url::to(['/ared/orden-compra/delete-upload', "id" => $model->_id, "att" => 'file_orden'])]],
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-lg-3">
                    <?=
                    $form->field($model, 'file_factura')->widget(FileInput::classname(), [
                        'pluginOptions' => [
                            'initialPreview' => empty($model->_id) ? false : $model->linkFactura,
                            'showUpload' => false,
                            'initialPreviewAsData' => !empty($model->_id),
                            'initialPreviewConfig' => empty($model->_id) ? false : [['type' => 'pdf', 'url' => Url::to(['/ared/orden-compra/delete-upload', "id" => $model->_id, "att" => 'file_factura'])]],
                        ]
                    ]);
                    ?>
                </div>
                <div class="col-lg-3">
                    <?=
                    $form->field($model, 'file_guia')->widget(FileInput::classname(), [
                        'pluginOptions' => [
                            'initialPreview' => empty($model->_id) ? false : $model->linkGuia,
                            'showUpload' => false,
                            'initialPreviewAsData' => !empty($model->_id),
                            'initialPreviewConfig' => empty($model->_id) ? false : [['type' => 'pdf', 'url' => Url::to(['/ared/orden-compra/delete-upload', "id" => $model->_id, "att" => 'file_guia'])]],
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <br>
    <fieldset id="presupuesto-detail" style="<?= ($model->isNewRecord) ? 'display: none' : '' ?> ">
        <legend>Información del presupuesto relacionado</legend>
        <div id="presupuesto-detail-view" ></div>
        <?php if (!$model->isNewRecord): ?>
            <?= $this->render('//cotizacion/_view', ['model' => $model->cotizacion]); ?>
        <?php endif; ?>
    </fieldset>

    <?php Pjax::begin(['id' => 'presupuesto-detail-view']); ?>
    <?php Pjax::end(); ?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
