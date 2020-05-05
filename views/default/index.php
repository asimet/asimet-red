<?php

use kartik\form\ActiveForm;
use kartik\switchinput\SwitchInput;
use yii\bootstrap\Html;
use unclead\multipleinput\MultipleInput;
use kartik\select2\Select2;
use kartik\file\FileInput;
use yii\helpers\Url;
use asimet\red\models\Partner;

$this->title = 'Asimet RED™';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="red-form">

    <?php $form = ActiveForm::begin(); ?>

    <label class="control-label" for="partner-accept">Accepto ser parte del grupo ASIMET RED y aprovechar la comunicación más facil con mis clientes y proveedores :</label>
    <?php
    echo $form->field($model, 'activo')->widget(SwitchInput::classname(), [
        'pluginOptions' => [
            'onColor' => 'success',
            'offColor' => 'danger',
            'onText' => 'Sí',
            'offText' => 'No',
        ]
    ])->label(false);
    ?>

    <div class="row">
        <div class="col-lg-5">
            <div class="row">
                <div class="col-lg-6">
                    <?php echo $form->field($model, 'rut')->textInput() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <?php echo $form->field($model, 'nombre')->textInput() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <?php echo $form->field($model, 'direccion')->textInput() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <?php echo $form->field($model, 'telefono')->textInput() ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <?php echo $form->field($model, 'correo')->textInput() ?>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <?=
            FileInput::widget([
                'name' => 'fileLogo',
                'options' => ['multiple' => 'false'],
                'pluginOptions' => [
                    'allowedFileExtensions' => ['jpg', 'jpeg', 'png', 'gif'],
                    'initialPreview' => (empty($model->_id)) ? false : $model->getUploads('link'),
                    'showUpload' => false,
                    'uploadUrl' => Url::to(['/ared/default/upload']),
                    'overwriteInitial' => false,
                    'initialPreviewAsData' => !empty($model->_id),
                    'initialPreviewConfig' => (empty($model->_id)) ? false : $model->getUploads('config'),
                    'validateInitialCount' => true,
                    'maxFileCount' => 1,
                    'autoReplace' => true,
                    'showCaption' => false,
                    'showRemove' => false,
                    'showUpload' => false,
                    'browseClass' => 'btn btn-primary btn-block',
                    'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                    'browseLabel' => 'Seleccionar Logo'
                ],
                'pluginEvents' => [
                    "filebatchselected" => 'function() { $(this).fileinput("upload"); }',
                ]
            ]);
            ?>
        </div>
    </div>
    <br>

    <?php if (!empty($model->_id)): ?>
        <fieldset>
            <legend>Mis Partners</legend>
            <?=
            MultipleInput::widget([
                'model' => $model,
                'id' => 'partner-multi',
                'attribute' => 'rulesList',
                'allowEmptyList' => false,
                'columns' => [
                    [
                        'title' => 'Partner',
                        'name' => 'partner',
                        'type' => Select2::classname(),
                        'options' => [
                            'data' => Partner::getPartnerOption(),
                            'pluginOptions' => [
                                'allowClear' => true,
                                'placeholder' => 'Partner ...',
                            ],
                        ],
                    ],
                    [
                        'title' => 'Role(s)',
                        'name' => 'role',
                        'type' => Select2::classname(),
                        'options' => [
                            'data' => $model->tipoOption,
                            'pluginOptions' => [
                                'allowClear' => true,
                                'tags' => true,
                                'multiple' => true,
                                'placeholder' => 'Role(s) ...',
                            ],
                        ],
                    ],
                ]
            ]);
            ?>
        </fieldset>
    <?php endif; ?>
    <br>

    <div class="form-group">
        <?= Html::submitButton(empty($model->_id) ? 'Crear' : 'Actualizar', ['class' => empty($model->_id) ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>