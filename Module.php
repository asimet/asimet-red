<?php

namespace asimet\red;

use Yii;
use yii\base\Module as BaseModule;
use asimet\red\models\Partner;

/**
 * onco module definition class
 */
class Module extends BaseModule {

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'asimet\red\controllers';
    public $adapter = '';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();
    }

    public function getUuid() {

        $uuid = Yii::$app->cache->get('uuid');

        if ($uuid === false) {
            $partner = Partner::findOne(["identity" => Yii::$app->id]);
            Yii::$app->cache->set('uuid', ((empty($partner)) ? null : $partner->_id));
        }

        return Yii::$app->cache->get('uuid');
    }

    public function getPerfil() {

        $perfil = Yii::$app->cache->get('perfil-red');

        if ($perfil === false) {
            $partner = Partner::findOne(["identity" => Yii::$app->id]);
            Yii::$app->cache->set('perfil-red', ((empty($partner)) ? null : $partner));
        }

        return Yii::$app->cache->get('perfil-red');
    }

    public function getLogo() {

        $logo = Yii::$app->cache->get('logo-red');

        if ($logo === false) {
            $partner = Partner::findOne(["identity" => Yii::$app->id]);
            Yii::$app->cache->set('logo-red', ((empty($partner)) ? null : $partner->zlogo));
        }

        return Yii::$app->cache->get('logo-red');
    }

    public function getActivo() {

        $activado = Yii::$app->cache->get('activado-red');

        if ($activado === false) {
            $partner = Partner::findOne(["identity" => Yii::$app->id]);
            Yii::$app->cache->set('activado-red', ((empty($partner)) ? "0" : $partner->activo));
        }

        return Yii::$app->cache->get('activado-red');
    }

    public function getIsActiv() {
        return ($this->getActivo() === "1");
    }

}
