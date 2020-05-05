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
class OrdenCompra extends ActiveRecord {

    public $file_orden;
    public $file_factura;
    public $file_guia;
    public $allItem;
    public $item;

    const ESTADO_EMITIDA = 1;
    const ESTADO_RECIBIDA = 2;
    const ESTADO_EN_PROCESO = 3;
    const ESTADO_FACTURADA = 4;
    const ESTADO_CANCELADA = 5;
    const ESTADO_TERMINADA = 6;
    // Estado BDD
    const PROCESADO_ACTIVADO = 1;
    const PROCESADO_PENDIENTE = 2;

    public static function bucketName() {
        return "orden-compra";
    }

    public function behaviors() {
        return [
            'uploadBehavior' => 'asimet\red\components\UploadBehavior'
        ];
    }

    /**
     * @return array list of attribute names.
     */
    public function attributes() {
        return [
            '_id',
            'estado_oc',
            'fecha_entrega',
            'dias_previo_aviso_entrega',
            'cliente_uuid',
            'proveedor_uuid',
            'cotizacion_uid',
            'items',
            'codigo_oc_externo',
            'identificador',
            'procesado',
            'filename_factura',
            'filename_orden',
            'filename_guia',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['cotizacion_uid', 'fecha_entrega'], 'required'],
            [['file_factura', 'file_guia', 'file_orden'], 'file', 'extensions' => 'pdf', 'skipOnEmpty' => true],
            [['fecha_entrega', 'estado_oc', 'cotizacion_uid', 'dias_previo_aviso_entrega', 'item', 'codigo_oc_externo', 'cliente_uuid', 'allItem'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            '_id' => 'ID',
            'proveedor.nombre' => 'Proveedor',
            'codigo_oc_externo' => 'Codigo',
            'identificador' => 'ID',
            'fecha_entrega' => 'Fecha Entrega',
            'dias_previo_aviso_entrega' => 'Dias Previo Aviso Entrega',
            'cotizacion_uid' => 'Cotización',
            'item' => 'Item',
            'estado_oc' => 'Estado',
            'estadoText' => 'Estado',
            'cotizacion.identificador' => 'Cotización',
            'allItem' => 'Todo los items',
            'file_orden' => 'Documento OC',
            'file_factura' => 'Documento Factura',
            'file_guia' => 'Documento Guía'
        ];
    }

    /**
     * 
     * @return Array
     */
    public function getEstadoOption() {
        return [
            self::ESTADO_EMITIDA => 'Emitida',
            self::ESTADO_RECIBIDA => 'Recibida',
            self::ESTADO_EN_PROCESO => 'En proceso',
            self::ESTADO_FACTURADA => 'Facturada',
            self::ESTADO_CANCELADA => 'Cancelada',
            self::ESTADO_TERMINADA => 'Terminada'
        ];
    }

    /**
     * 
     * @return Array
     */
    public function getColorOption() {
        return [
            self::ESTADO_EMITIDA => 'GreenYellow',
            self::ESTADO_RECIBIDA => 'lightgray',
            self::ESTADO_EN_PROCESO => 'En yellow',
            self::ESTADO_FACTURADA => 'GreenYellow',
            self::ESTADO_CANCELADA => 'red',
            self::ESTADO_TERMINADA => 'cyan'
        ];
    }

    public function getEstadoText() {
        $options = $this->estadoOption;
        return (isset($options[$this->estado_oc])) ? $options[$this->estado_oc] : "Desconocido";
    }

    public function getEstadoColor() {
        $options = $this->colorOption;
        return (isset($options[$this->estado_oc])) ? $options[$this->estado_oc] : "white";
    }

    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {

            if ($insert) {

                $identificador = Yii::$app->couchbase->getBucket('orden-compra')->bucket->counter('ordencompra_id', 1, ['initial' => 100]);
                $this->identificador = $identificador->value;

                if (empty($this->estado_oc)) {
                    $this->estado_oc = self::ESTADO_EMITIDA;
                }
                if (empty($this->procesado)) {
                    $this->procesado = self::PROCESADO_PENDIENTE;
                }
            }

            $cotizacion = Cotizacion::FindOne($this->cotizacion_uid);
            $this->proveedor_uuid = $cotizacion->partner->_id;
            $items = [];

            if (!empty($this->allItem)) {
                foreach ($cotizacion->getItemOption([Cotizacion::ESTADO_ITEM_ACEPTADO]) as $itemCotizacion) {
                    $items[] = $itemCotizacion['id'];
                }
            } else {
                $items[] = $this->item;
            }

            $this->items = $items;

            return true;
        }

        return false;
    }

    public function afterFind() {

        $itemsCotizacion = [];
        $itemsOrdenCompra = $this->items;

        foreach ($this->cotizacion->getItemOption([Cotizacion::ESTADO_ITEM_ACEPTADO]) as $itemCotizacion) {
            $itemsCotizacion[] = $itemCotizacion['id'];
        }

        sort($itemsCotizacion);
        sort($itemsOrdenCompra);
        $this->allItem = ($itemsCotizacion == $itemsOrdenCompra);
        $this->item = $itemsOrdenCompra[0];

        parent::afterFind();
    }

    public function getCotizacion() {
        return Cotizacion::FindOne($this->cotizacion_uid);
    }

    public function getProveedor() {
        return Partner::FindOne($this->proveedor_uuid);
    }

    public function getCliente() {
        return Partner::FindOne($this->cliente_uuid);
    }

    public function getLinkGuia() {

        if (!empty($this->filename_guia)) {
            return 'https://archivos.cetasimet.cl/' . $this->filename_guia;
        }

        return false;
    }

    public function getLinkOrden() {

        if (!empty($this->filename_orden)) {
            return 'https://archivos.cetasimet.cl/' . $this->filename_orden;
        }

        return false;
    }

    public function getLinkFactura() {

        if (!empty($this->filename_factura)) {
            return 'https://archivos.cetasimet.cl/' . $this->filename_factura;
        }

        return false;
    }

    public function uploadGuia($file) {
        $this->upload($file);
        $this->filename_guia = $this->filename;
        return $this->filename_guia;
    }

    public function uploadOrden($file) {
        $this->upload($file);
        $this->filename_orden = $this->filename;
        return $this->filename_orden;
    }

    public function uploadFactura($file) {
        $this->upload($file);
        $this->filename_factura = $this->filename;
        return $this->filename_factura;
    }

    public function deleteGuia() {
        $this->deleteFile($this->filename_guia);
        $this->filename_guia = "";
        $this->update(false);
    }

    public function deleteOrden() {
        $this->deleteFile($this->filename_orden);
        $this->filename_orden = "";
        $this->update(false);
    }

    public function deleteFactura() {
        $this->deleteFile($this->filename_factura);
        $this->filename_factura = "";
        $this->update(false);
    }

}
