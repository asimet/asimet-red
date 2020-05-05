<?php

use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\data\ArrayDataProvider;
use asimet\red\models\OrdenTrabajo;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenTrabajo */

$this->title = 'OT Nº' . $model->identificador;
$this->params['breadcrumbs'][] = ['label' => 'Ordenes de Trabajo', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-trabajo-view">

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'identificador',
            'proveedor.nombre',
            'cotizacion.identificador',
            'fecha_ingreso:date',
            'fecha_inicio:date',
            'fecha_entrega:date',
            'estadoText'
        ],
    ])
    ?>

    <br>
    <fieldset>
        <legend>Procesos internos</legend>
        <?=
        GridView::widget([
            'dataProvider' => new ArrayDataProvider(['key' => 'id', 'allModels' => $model->procesos]),
            'columns' => [
                'descripcion_general',
                'fecha_termino_estimada:datetime',
                'porcentaje_avance',
                [
                    'label' => 'Estado cumplimiento',
                    'format' => 'raw',
                    'value' => function ($model) {
                        $estados = OrdenTrabajo::getEstadoOption();
                        return (isset($estados[$model['estado_cumplimiento']]) ? $estados[$model['estado_cumplimiento']] : 'Desconocido');
                    }
                ]
            ]
        ]);
        ?>
    </fieldset>

</div>
