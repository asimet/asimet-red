<?php

namespace asimet\red\controllers;

use Yii;
use app\components\BaseController;
use yii\filters\VerbFilter;
use asimet\red\models\Partner;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Description of RedController
 *
 * @author gilles
 */
class DefaultController extends BaseController {

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
     * Index indicaciones RED Asimet
     * @return type
     */
    public function actionIndex() {

        $model = Partner::findOne(["identity" => Yii::$app->id]) ?? new Partner();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->getSession()->setFlash('success', 'La operación se realizó con éxito');
                return $this->render('index', ['model' => $model]);
            } else {
                Yii::$app->getSession()->setFlash('error', 'Se ha producido un error al realizar la operación');
            }
        }

        return $this->render('index', ['model' => $model]);
    }

    public function actionUpload() {

        $error = '';
        Yii::$app->response->format = Response::FORMAT_JSON;

        $file = UploadedFile::getInstanceByName('fileLogo');

        $extension = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
        $fileLogo = 'data:image/' . $extension . ';base64,' . base64_encode(file_get_contents($file->tempName));
        Yii::$app->session[Partner::LOGO_UPLOAD] = $fileLogo;

        Yii::$app->response->data = ['error' => $error];
    }

}
