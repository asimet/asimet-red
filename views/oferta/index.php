<?php

use yii\helpers\Html;
use yii\web\View;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OfertaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Elaborar cotización';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="oferta-index">

    <?php if (intval(Yii::$app->getModule('ared')->perfil->nivel) < 1): ?>
        <p>
            <?= Html::a('Crear Solicitud de cotización', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
        <?=
        ExportMenu::widget([
            'dataProvider' => $sended,
            'columns' => [
                'code',
                'identificador',
                'owner.nombre',
                'fecha_ingreso:datetime',
                'fecha_cierre:datetime',
                'fecha_entrega:date',
                [
                    'attribute' => 'estado',
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
        <br>
        <fieldset>
            <legend>Solicitudes oferta enviadas</legend>
            <?=
            GridView::widget([
                'dataProvider' => $sended,
                'columns' => [
                    'code',
                    'identificador',
                    'owner.nombre',
                    'fecha_ingreso:datetime',
                    'fecha_cierre:datetime',
                    'fecha_entrega:date',
                    [
                        'attribute' => 'estado',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '<span style="background:' . $model->colorText . '">' . $model->estadoText . '</span>';
                        }
                    ],
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            ?>
        </fieldset>
    <?php endif; ?>
    <br>
    <fieldset>
        <legend>Solicitudes oferta recibidas</legend>
        <?=
        GridView::widget([
            'dataProvider' => $received,
            'columns' => [
                'code',
                'identificador',
                'owner.nombre',
                'fecha_ingreso:datetime',
                'fecha_cierre:datetime',
                'fecha_entrega:date',
                [
                    'attribute' => 'estado',
                    'format' => 'raw',
                    'value' => function ($model) {
                        return '<span style="background:' . $model->colorText . '">' . $model->estadoText . '</span>';
                    }
                ],
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
        ?>
    </fieldset>

</div>
