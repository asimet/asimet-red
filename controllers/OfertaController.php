<?php

namespace asimet\red\controllers;

use Yii;
use asimet\red\models\Cotizacion;
use asimet\red\models\Oferta;
use asimet\red\models\OfertaSearch;
use app\components\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use phpseclib\Net\SFTP;
use phpseclib\Crypt\RSA;
use yii\helpers\Url;
use yii\web\Response;

/**
 * OfertaController implements the CRUD actions for Oferta model.
 */
class OfertaController extends BaseController {

    public function behaviors() {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * Lists all Oferta models.
     * @return mixed
     */
    public function actionIndex() {

        if (empty(Yii::$app->getModule('ared')->uuid)) {
            return $this->render('no-red');
        }

        $searchModel = new OfertaSearch();
        $sended = $searchModel->search(Yii::$app->request->queryParams);
        $received = $searchModel->searchReceived(Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'sended' => $sended,
                    'received' => $received,
        ]);
    }

    /**
     * Displays a single Oferta model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Oferta model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {

        $model = new Oferta();
        $model->cliente = Yii::$app->getModule('ared')->uuid;
        $model->fecha_ingreso = (new \DateTime("now", new \DateTimeZone("America/Santiago")))->format("Y-m-d H:i:s");

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
     * Updates an existing Oferta model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'La operación se realizó con éxito');
                return $this->redirect(['view', 'id' => $model->_id]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Se ha producido un error al realizar la operación');
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionCotizacionState($id) {

        $model = $this->findModel($id);
        $cotizacionId = Yii::$app->request->post("uuid");
        $cotizacion = Cotizacion::findOne($cotizacionId);
        $otherState = Cotizacion::ESTADO_VIGENTE;

        switch ($cotizacion->estado_cotizacion) {
            case Cotizacion::ESTADO_VIGENTE :
                $cotizacion->estado_cotizacion = Cotizacion::ESTADO_ACEPTADA;
                $otherState = Cotizacion::ESTADO_RECHAZADA;
                break;
            case Cotizacion::ESTADO_RECHAZADA :
                $cotizacion->estado_cotizacion = Cotizacion::ESTADO_VIGENTE;
                break;
            case Cotizacion::ESTADO_ACEPTADA :
                $cotizacion->estado_cotizacion = Cotizacion::ESTADO_VIGENTE;
                break;
        };

        // Cambiar los estados de los items en caso de aceptación
        if ($cotizacion->estado_cotizacion === Cotizacion::ESTADO_ACEPTADA) {
            $cotizacion->changeEstadoItem(Cotizacion::ESTADO_ITEM_ACEPTADO);
        }

        $cotizacion->save();

        // Cambiar los estados de las otras cotizaciones relacionadas con la oferta
        foreach ($model->cotizaciones as $cotizacion) {
            if ($cotizacion['uuid'] !== $cotizacionId) {
                $cotiz = Cotizacion::findOne($cotizacion['uuid']);
                if (!empty($cotiz)) {
                    $cotiz->estado_cotizacion = $otherState;
                    $cotiz->save();
                }
            }
        }
    }

    public function actionPullCotizar($uuid) {

        $adapterClass = Yii::$app->getModule('ared')->adapter;
        $adapter = new $adapterClass();
        $adapter->pull($uuid);
    }

    public function actionUpload() {

        $error = '';
        $preview = [];
        $config = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $files = UploadedFile::getInstancesByName('medias');

        foreach ($files as $file) {

            $ssh = new SFTP('archivos.cetasimet.cl');
            $key = new RSA();
            $key->setPassword('0662emeva$$');
            $key->loadKey(Oferta::PRIVATE_KEY);

            if ($ssh->login('archive', $key)) {

                $extension = pathinfo($file->name, PATHINFO_EXTENSION);
                $Filename = Yii::$app->security->generateRandomString() . ".{$extension}";
                $path = '/var/www/html/archives/' . $Filename;
                $key = uniqid();

                $ssh->put($path, $file->tempName, SFTP::SOURCE_LOCAL_FILE);

                $media = [];
                $media["key"] = $key;
                $media["path"] = $path;
                $media["caption"] = $file->name;
                $media["size"] = $file->size;
                $media["type"] = Oferta::getType($extension);
                $media["downloadUrl"] = "https://archivos.cetasimet.cl/$Filename";
                $media["url"] = Url::to(['/ared/oferta/delete-upload', "id" => $key]);

                $preview[] = "https://archivos.cetasimet.cl/$Filename";
                $config[] = $media;

                Oferta::addArchiveSession($key, $media);
            } else {
                $error = 'Error para transferir el archivo subido';
            }

            if (!empty($error)) {
                break;
            }
        }

        Yii::$app->response->data = ['error' => $error, 'initialPreview' => $preview, 'initialPreviewConfig' => $config, 'initialPreviewAsData' => true];
    }

    public function actionDeleteUpload($id) {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $error = '';
        $session = Yii::$app->session;
        $uploads = ($session->has(Oferta::ARCHIVES_UPLOADS)) ? $session[Oferta::ARCHIVES_UPLOADS] : [];

        $ssh = new SFTP('archivos.cetasimet.cl');
        $key = new RSA();
        $key->setPassword('0662emeva$$');
        $key->loadKey(Oferta::PRIVATE_KEY);

        if ($ssh->login('archive', $key) && isset($uploads[$id])) {
            $ssh->delete($uploads[$id]["path"]);
            unset($uploads[$id]);
        } else {
            $error = 'Error para borrar el archivo subido';
        }

        $session[Oferta::ARCHIVES_UPLOADS] = $uploads;
        Yii::$app->response->data = ['error' => $error];
    }

    /**
     * Deletes an existing Oferta model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Oferta model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Oferta the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Oferta::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
