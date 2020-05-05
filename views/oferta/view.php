<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Oferta */

$this->title = 'Oferta ' . $model->identificador;
$this->params['breadcrumbs'][] = ['label' => 'Solicitudes de cotización', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="oferta-view">

    <p>
        <?= Html::a('<i class="fa fa-fw fa-pencil-square-o" aria-hidden="true"></i>' . Yii::t('yii', 'Update'), ['update', 'id' => $model->_id], ['class' => 'btn btn-primary']) ?>
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
            'code',
            'identificador',
            'fecha_ingreso:datetime',
            'fecha_cierre:datetime',
            'fecha_entrega:date',
            'estadoText',
            'detalle:ntext',
        ],
    ])
    ?>

</div>
