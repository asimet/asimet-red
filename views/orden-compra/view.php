<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\OrdenCompra */

$this->title = $model->identificador;
$this->params['breadcrumbs'][] = ['label' => 'Orden Compras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-compra-view">

    <p>
        <?= Html::a('<i class="fa fa-fw fa-pencil-square-o" aria-hidden="true"></i>' . Yii::t('yii', 'Editar'), ['update', 'id' => $model->_id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('<i class="fa fa-fw fa-trash-o" aria-hidden="true"></i>' . Yii::t('yii', 'Delete'), ['delete', 'id' => $model->_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'identificador',
            'cotizacion.identificador',
            'cotizacion.partner.nombre',
            'codigo_oc_externo',
            'estadoText',
            [
                'label' => 'Orden OC',
                'format' => 'html',
                'visible' => !empty($model->filename_orden),
                'value' => Html::a($model->filename_orden, $model->linkOrden),
            ],
            [
                'label' => 'Guía OC',
                'format' => 'html',
                'visible' => !empty($model->filename_guia),
                'value' => Html::a($model->filename_guia, $model->linkGuia),
            ],
            [
                'label' => 'Factura OC',
                'format' => 'html',
                'visible' => !empty($model->filename_factura),
                'value' => Html::a($model->filename_factura, $model->linkFactura),
            ],
            'fecha_entrega:date',
            'dias_previo_aviso_entrega',
        ],
    ])
    ?>

</div>
