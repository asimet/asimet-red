<?php

use yii\web\View;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use unclead\multipleinput\MultipleInput;
use kartik\select2\Select2;
use kartik\date\DatePicker;
use kartik\file\FileInput;
use yii\helpers\Url;
use kartik\grid\GridView;
use asimet\red\models\Oferta;
use yii\data\ArrayDataProvider;
use kartik\switchinput\SwitchInput;
use asimet\red\models\Cotizacion;

$dataProvider = new ArrayDataProvider(['allModels' => $model->cotizaciones]);
$this->registerCssFile("@web/css/loader.css");

$this->registerJs("
var loading = $('#loader').hide();
$(document)
  .ajaxStart(function () {
    loading.show();
  })
  .ajaxStop(function () {
    loading.hide();
  });
;", View::POS_READY, 'loader-ajax');


$this->registerJs("
function changeSwitchState(sw) {
    
    var currentId = sw.attr('id');
    var currentVal = $('#' + currentId).bootstrapSwitch('state');
    var currentText = $('#' + currentId).bootstrapSwitch('onText');
         
    var offColor = 'danger';
    var onColor = 'info';
    var offText = 'Rechazada';
    var onText = 'Pendiente';

    if(currentVal == false && currentText == 'Aceptada') {
        currentText = 'Pendiente';
    }

    if(currentText == 'Pendiente') {
        offColor = 'info';
        onColor = 'success';
        onText = 'Aceptada';
        offText = 'Pendiente';
    }
    
    $('[id^=sw_]').each(function() {
        var elementId = $(this).attr('id');
        if(elementId != currentId || currentText == 'Pendiente') {
            $('#' + elementId).bootstrapSwitch('offColor', offColor);
            $('#' + elementId).bootstrapSwitch('onColor', onColor);
            $('#' + elementId).bootstrapSwitch('offText', offText);
            $('#' + elementId).bootstrapSwitch('onText', onText);
            $('#' + elementId).bootstrapSwitch('state', false, true);
        }
    });
}
", View::POS_END, 'changeSwitchState');
?>

<div id="loader" class="loading">Loading...</div>

<div class="oferta-form">

    <?php $form = ActiveForm::begin(['options' => ['autocomplete' => "off"]]); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-3">
                    <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'disabled' => !$model->isOwner()]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="row">
                <div class="col-lg-3">
                    <?= $form->field($model, 'fecha_ingreso')->widget('kartik\\datecontrol\\DateControl', ['type' => 'datetime', 'disabled' => true]) ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'fecha_cierre')->widget('kartik\\datecontrol\\DateControl', ['type' => 'datetime', 'disabled' => !$model->isOwner()]) ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'fecha_entrega')->widget('kartik\\datecontrol\\DateControl', ['type' => 'date', 'disabled' => !$model->isOwner()]) ?>
                </div>
                <div class="col-lg-3">
                    <?= $form->field($model, 'estado')->dropDownList($model->estadoOption, ['prompt' => 'Seleccione', 'disabled' => !$model->isOwner()]) ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <?= $form->field($model, 'detalle')->textarea(['rows' => 6, 'disabled' => !$model->isOwner()]) ?>
        </div>
    </div>

    <fieldset>
        <legend>Información de ítems</legend>
        <?=
        MultipleInput::widget([
            'model' => $model,
            'id' => 'items-multi',
            'attribute' => 'itemList',
            'allowEmptyList' => false,
            'columns' => [
                [
                    'title' => 'Cantidad',
                    'name' => 'cantidad',
                    'headerOptions' => [
                        'style' => 'width: 100px;',
                    ],
                    'options' => [
                        'disabled' => !$model->isOwner()
                    ]
                ],
                [
                    'title' => 'Descripción / Especificaciones',
                    'name' => 'descripcion',
                    'options' => [
                        'disabled' => !$model->isOwner()
                    ]
                ],
                [
                    'title' => 'Fecha entrega item',
                    'name' => 'fecha_entrega',
                    'type' => DatePicker::className(),
                    'value' => function($data) {
                        return $data['fecha_entrega'];
                    },
                    'options' => [
                        'disabled' => !$model->isOwner(),
                        'pluginOptions' => [
                            'todayHighlight' => true
                        ]
                    ],
                    'headerOptions' => [
                        'style' => 'width: 200px;',
                    ],
                ],
            ]
        ]);
        ?>
        <br>
    </fieldset>

    <div class="row">
        <div class="col-lg-6">
            <?=
            FileInput::widget([
                'name' => 'medias',
                'language' => 'es',
                'options' => ['multiple' => true],
                'pluginOptions' => [
                    'initialPreview' => empty($model->_id) ? false : $model->getUploads('link'),
                    'previewFileType' => 'any',
                    'showUpload' => false,
                    'uploadUrl' => Url::to(['/ared/oferta/upload']),
                    'overwriteInitial' => false,
                    'initialPreviewAsData' => true,
                    'initialPreviewConfig' => empty($model->_id) ? false : $model->getUploads('config'),
                ],
                'pluginEvents' => [
                    "filebatchselected" => 'function() { $(this).fileinput("upload"); }',
                ]
            ]);
            ?>
        </div>
    </div>
    <br>

    <?php if ($model->isOwner()): ?>
        <div class="row">
            <div class="col-lg-6">
                <fieldset>
                    <legend>Proveedores licitados</legend>
                    <?=
                    MultipleInput::widget([
                        'model' => $model,
                        'attribute' => 'proveedorList',
                        'allowEmptyList' => false,
                        'columns' => [
                            [
                                'name' => 'proveedor',
                                'type' => Select2::classname(),
                                'title' => 'Proveedor',
                                'options' => [
                                    'data' => $model->empresaRedOption,
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'placeholder' => 'Proveedor ...',
                                    ],
                                ],
                            ],
                        ]
                    ]);
                    ?>
                    <br>
                </fieldset>
            </div>
        </div>
        <br>
        <?php if (!empty($model->cotizaciones)): ?>
            <div class="row">
                <div class="col-lg-6">
                    <fieldset>
                        <legend>Cotizaciones recibidas de la solicitud</legend>
                        <?=
                        GridView::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => [
                                [
                                    'attribute' => 'Proveedor',
                                    'format' => 'raw',
                                    'value' => function ($data) {
                                        return Oferta::getEmpresaRedOption()[$data["proveedor"]];
                                    }
                                ],
                                'precio:currency',
                                [
                                    'label' => 'Estado',
                                    'format' => 'raw',
                                    'value' => function ($data) use ($model) {
                                        return SwitchInput::widget(
                                                        [
                                                            'name' => 'estado',
                                                            'value' => (Cotizacion::findOne($data["uuid"])->estado_cotizacion === Cotizacion::ESTADO_ACEPTADA),
                                                            'pluginOptions' => [
                                                                'onText' => (Cotizacion::findOne($data["uuid"])->estado_cotizacion === Cotizacion::ESTADO_RECHAZADA) ? 'Pendiente' : 'Aceptada',
                                                                'offText' => (Cotizacion::findOne($data["uuid"])->estado_cotizacion === Cotizacion::ESTADO_RECHAZADA) ? 'Rechazada' : 'Pendiente',
                                                                'onColor' => (Cotizacion::findOne($data["uuid"])->estado_cotizacion === Cotizacion::ESTADO_RECHAZADA) ? 'info' : 'success',
                                                                'offColor' => (Cotizacion::findOne($data["uuid"])->estado_cotizacion === Cotizacion::ESTADO_RECHAZADA) ? 'danger' : 'info',
                                                                'size' => 'mini'
                                                            ],
                                                            'options' => [
                                                                'id' => 'sw_' . $data["uuid"],
                                                            ],
                                                            'pluginEvents' => [
                                                                "switchChange.bootstrapSwitch" => "function() { "
                                                                . "changeSwitchState($(this));"
                                                                . "$.post('" . Url::to(["/ared/oferta/cotizacion-state", "id" => $model->_id]) . "',{uuid:'" . $data["uuid"] . "'}); "
                                                                . "}",
                                                            ],
                                                            'containerOptions' => [
                                                                'style' => 'margin-bottom:0px;'
                                                            ]
                                        ]);
                                    }
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'headerOptions' => ['style' => 'width:15%'],
                                    'template' => '{view} {print}', // the default buttons + your custom button
                                    'buttons' => [
                                        'print' => function($url, $model, $key) {
                                            return Html::a('<i class="fa fa-print"></i>', ['/cotizacion/print', 'id' => $model["uuid"]], ['title' => "Imprimir"]);
                                        },
                                        'view' => function($url, $model, $key) {
                                            return Html::a('<i class="fa fa-eye"></i>', ['/cotizacion/view', 'id' => $model["uuid"]], ['title' => "Imprimir"]);
                                        },
                                    ],
                                ]
                            ],
                        ]);
                        ?>
                        <br>
                    </fieldset>
                </div>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($model->isOwner()): ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Crear' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php else: ?>
        <?php if (!$model->haveCotizacion() && $model->estado != Oferta::ESTADO_CERRADA && $model->estado != Oferta::ESTADO_ASIGNADA): ?>
            <div class="form-group">
                <?= Html::a('<i class="fa fa-fw fa-pencil-square-o" aria-hidden="true"></i>Crear Cotización', ['/ared/oferta/pull-cotizar', 'uuid' => $model->_id], ['class' => 'btn btn-primary']) ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php ActiveForm::end(); ?>

</div>
