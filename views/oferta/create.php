<?php

/* @var $this yii\web\View */
/* @var $model app\models\Oferta */

$this->title = 'Crear Solicitud de cotización';
$this->params['breadcrumbs'][] = ['label' => 'Ofertas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="oferta-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
