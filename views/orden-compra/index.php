<?php

use yii\helpers\Html;
use yii\web\View;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrdenCompraSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Orden Compras';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-compra-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
        <?= Html::a('Crear Orden Compra', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'identificador',
            'cotizacion.identificador',
            'proveedor.nombre',
            'estadoText',
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
            'codigo_oc_externo',
            'cotizacion.identificador',
            'proveedor.nombre',
            'estadoText',
            /*
              [
              'attribute' => 'url_documento_oc',
              'format' => 'raw',
              'value' => function ($model) {
              return Html::a($model->url_documento_oc, $model->documentoLink); //'<span style="background:' . $model->colorText . '">' . $model->estadoText . '</span>';
              }
              ], */
            ['class' => 'yii\grid\ActionColumn', 'headerOptions' => ['style' => 'width:15%']],
        ],
    ]);
    ?>

</div>
