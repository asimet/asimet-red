<?php
/* @var $this yii\web\View */
/* @var $model app\models\Oferta */

$this->title = 'Editar Solicitud de cotización : ' . ' ' . $model->owner->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Ofertas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->identificador, 'url' => ['view', 'id' => $model->_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="oferta-update">

<?=
$this->render('_form', [
    'model' => $model,
])
?>

</div>
