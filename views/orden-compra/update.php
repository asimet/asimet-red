<?php

/* @var $this yii\web\View */
/* @var $model app\models\OrdenCompra */

$this->title = 'Actualizar Orden Compra: ' . ' ' . $model->identificador;
$this->params['breadcrumbs'][] = ['label' => 'Orden Compras', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->identificador, 'url' => ['view', 'id' => $model->identificador]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="orden-compra-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
