<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace asimet\red\controllers;

use Yii;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use asimet\red\models\Cotizacion;
use asimet\red\models\OrdenCompra;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use asimet\red\models\OrdenCompraSearch;

/**
 * Description of OrdenTrabajoController
 *
 * @author gilles
 */
class OrdenCompraController extends BaseController {

    public function behaviors() {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ]
        ]);
    }

    /**
     * Lists all Oferta models.
     * @return mixed
     */
    public function actionIndex() {

        $searchModel = new OrdenCompraSearch();
        $dataProvider = $searchModel->searchCliente(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single OrdenTrabajo model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Deletes an existing OrdenTrabajo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Creates a new OrdenCompra model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {

        $model = new OrdenCompra();
        $model->cliente_uuid = Yii::$app->getModule('ared')->uuid;

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'La operación se realizó con éxito');
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Se ha producido un error al realizar la operación');
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing OrdenCompra model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {

        $model = $this->findModel($id);
        $model->cliente_uuid = Yii::$app->getModule('ared')->uuid;

        if ($model->load(Yii::$app->request->post())) {
            $this->uploadFile($model);
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'La operación se realizó con éxito');
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Se ha producido un error al realizar la operación');
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    private function uploadFile($model) {

        $file_factura = UploadedFile::getInstance($model, 'file_factura');
        $file_guia = UploadedFile::getInstance($model, 'file_guia');
        $file_orden = UploadedFile::getInstance($model, 'file_orden');

        if (!empty($file_factura)) {
            $model->uploadFactura($file_factura);
        }
        if (!empty($file_guia)) {
            $model->uploadGuia($file_guia);
        }
        if (!empty($file_orden)) {
            $model->uploadOrden($file_orden);
        }
    }

    public function actionDeleteUpload($id, $att) {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $error = '';
        $model = $this->findModel($id);

        if ($att == 'file_orden') {
            $model->deleteOrden();
        }
        if ($att == 'file_factura') {
            $model->deleteFactura();
        }
        if ($att == 'file_guia') {
            $model->deleteGuia();
        }

        Yii::$app->response->data = ['error' => $error];
    }

    public function actionItem() {

        $out = [];

        Yii::$app->response->format = Response::FORMAT_JSON;

        if (isset($_POST['depdrop_all_params'])) {
            $parents = $_POST['depdrop_all_params'];
            $selected = $_POST['depdrop_all_params']['selected_item_id'];
            if ($parents != null) {

                $cotizacion_id = $parents['ordencompra-cotizacion_uid'];
                $cotizacion = Cotizacion::findOne($cotizacion_id);
                $out = $cotizacion->getItemOption([Cotizacion::ESTADO_ITEM_ACEPTADO]);

                Yii::$app->response->data = ['output' => $out, 'selected' => $selected];
                return;
            }
        }

        Yii::$app->response->data = ['output' => '', 'selected' => ''];
    }

    /**
     * Finds the OrdenTrabajo model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Oferta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = OrdenCompra::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
