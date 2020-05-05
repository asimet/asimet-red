<?php

namespace asimet\red\models;

use Yii;
use matrozov\couchbase\ActiveRecord;
use asimet\red\models\Partner;

/**
 * This is the model class for table "oferta".
 *
 * @property integer $id
 * @property string $fecha_cierre
 * @property string $fecha_entrega
 * @property integer $estado
 * @property string $detalle
 */
class OrdenTrabajo extends ActiveRecord {

    public $tipo;
    public $cotizacion_id;
    public $item_id;
    public $orden_compra_id;

    const TIPO_NORMAL = "1";
    const TIPO_EMERGENCIA = "2";
    // Estado
    const ESTADO_SIN_INICIAR = 1;
    const ESTADO_EN_EJECUCION = 2;
    const ESTADO_ADVERTENCIA = 3;
    const ESTADO_ATRASADA = 4;
    const ESTADO_EN_REPROCESO = 5;
    const ESTADO_TERMINADA = 6;
    // Estado BDD
    const PROCESADO_ACTIVADO = 1;
    const PROCESADO_PENDIENTE = 2;

    public static function bucketName() {
        return "orden-trabajo";
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes() {
        return ['_id', 'tipo', 'estado_cumplimiento', 'fecha_ingreso', 'fecha_inicio', 'fecha_entrega', 'cotizacion_uid', 'item', 'cliente_uuid', 'proveedor_uuid', 'procesos', 'identificador', 'procesado'];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['fecha_inicio', 'fecha_entrega'], 'required'],
            [['fecha_cierre', 'fecha_entrega', 'estado_cumplimiento', 'procesos', 'cotizacion_uid', 'item'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            '_id' => 'ID',
            'identificador' => 'ID',
            'fecha_ingreso' => 'Fecha Ingreso',
            'fecha_inicio' => 'Fecha Inicio',
            'fecha_entrega' => 'Fecha estimada Termino',
            'tipo' => 'Tipo prioridad OT',
            'cotizacion_uid' => 'Cotización',
            'item' => 'Item',
            'orden_compra_id' => 'Orden Compra',
            'estadoText' => 'Estado Cumplimiento',
            'proveedor.nombre' => 'Proveedor',
            'cotizacion.identificador' => 'Cotización'
        ];
    }

    /**
     * 
     * @return Array
     */
    public static function getEstadoOption() {
        return [
            self::ESTADO_SIN_INICIAR => 'Sin iniciar',
            self::ESTADO_EN_EJECUCION => 'En ejecución',
            self::ESTADO_ADVERTENCIA => 'Advertencia',
            self::ESTADO_ATRASADA => 'Atrasada',
            self::ESTADO_EN_REPROCESO => 'En reproceso',
            self::ESTADO_TERMINADA => 'Terminada',
        ];
    }

    /**
     * 
     * @return Array
     */
    public function getColorOption() {
        return [
            self::ESTADO_SIN_INICIAR => 'lightgray',
            self::ESTADO_EN_EJECUCION => 'GreenYellow',
            self::ESTADO_ADVERTENCIA => 'yellow',
            self::ESTADO_ATRASADA => 'red',
            self::ESTADO_TERMINADA => 'cyan',
            self::ESTADO_EN_REPROCESO => 'firebrick',
        ];
    }

    /**
     * 
     * @return Array
     */
    public function getTipoOption() {
        return [
            self::TIPO_NORMAL => 'Normal c/Cotización Aprobada',
            self::TIPO_EMERGENCIA => 'Cotización Emergencia',
        ];
    }

    /**
     * 
     * @return Array
     */
    public function getTipoColorOption() {
        return [
            self::TIPO_NORMAL => 'GreenYellow',
            self::TIPO_EMERGENCIA => 'Red',
        ];
    }

    public function getTipoText() {
        $options = $this->tipoOption;
        return (isset($options[$this->tipo])) ? $options[$this->tipo] : "Desconocido";
    }

    public function getEstadoText() {
        $options = $this->estadoOption;
        return (isset($options[$this->estado_cumplimiento])) ? $options[$this->estado_cumplimiento] : "Desconocido";
    }

    public function getColorText() {
        $options = $this->tipoColorOption;
        return (isset($options[$this->tipo])) ? $options[$this->tipo] : "white";
    }

    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {

            if ($insert) {

                $this->fecha_ingreso = (new \DateTime("now", new \DateTimeZone("America/Santiago")))->format("Y-m-d");

                if (empty($this->estado_cumplimiento)) {
                    $this->estado_cumplimiento = self::ESTADO_SIN_INICIAR;
                }
                if (empty($this->procesado)) {
                    $this->procesado = self::PROCESADO_PENDIENTE;
                }
                if (empty($this->cliente_uuid)) {
                    $this->cliente_uuid = Yii::$app->getModule('ared')->uuid;
                }
                if (!empty($this->cotizacion_uid) && empty($this->proveedor_uuid)) {
                    $this->proveedor_uuid = $this->cotizacion->proveedor_uuid;
                }

                $identificador = Yii::$app->couchbase->getBucket('orden-trabajo')->bucket->counter('ordentrabajo_id', 1, ['initial' => 100]);
                $this->identificador = $identificador->value;
            }

            return true;
        }

        return false;
    }

    public function afterFind() {

        parent::afterFind();
    }

    public function getCotizacion() {
        return Cotizacion::FindOne($this->cotizacion_uid);
    }

    public function getProveedor() {
        return Partner::FindOne($this->proveedor_uuid);
    }

    public function saveProceso($model) {

        $procesos = $this->procesos ?: [];

        if (!empty($procesos)) {
            $procesos = array_filter($procesos, function($v) use ($model) {
                return $v['id'] != $model->id;
            });
        }

        $procesos[] = $model->toArray();
        $this->procesos = $procesos;
        $this->update();
    }

}
