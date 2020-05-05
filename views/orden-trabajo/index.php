<?php

use yii\helpers\Html;
use yii\web\View;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrdenTrabajoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Registros de ordenes de trabajo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-trabajo-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
        <?= Html::a('Crear nueva OT', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'identificador',
            'proveedor.nombre',
            'cotizacion.identificador',
            'fecha_ingreso:date',
            'fecha_inicio:date',
            'fecha_entrega:date',
            [
                'attribute' => 'estado_cumplimiento',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<span style="background:' . $model->colorText . '">' . $model->estadoText . '</span>';
                }
            ],
    ]]);
    ?>

    <?php $this->registerJs("$('.search-button').click(function(){ $('.search-form').toggle(); return false; });", View::POS_READY, 'searchBaseScriptBoilerplate'); ?>

    <div class="btn-group" role="group">
        <button id="w3-cols" class="btn btn-default dropdown-toggle search-button">
            <i class="glyphicon glyphicon-search"></i>
        </button>
    </div>

    <div class="row">
        <div class="search-form col-lg-6" style="display:none">
            <?php echo $this->render('_search', ['model' => $searchModel]); ?>
        </div>
    </div>


    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'identificador',
            'proveedor.nombre',
            'cotizacion.identificador',
            'fecha_ingreso:date',
            'fecha_inicio:date',
            'fecha_entrega:date',
            [
                'attribute' => 'estado_cumplimiento',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<span style="background:' . $model->colorText . '">' . $model->estadoText . '</span>';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'headerOptions' => ['style' => 'width:15%'],
            ]
        ],
    ]);
    ?>

</div>
