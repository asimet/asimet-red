<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\OrdenCompra */

$this->title = 'Crear Orden Compra';
$this->params['breadcrumbs'][] = ['label' => 'Orden Compras', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="orden-compra-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
