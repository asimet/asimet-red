<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace asimet\red\models;

use Yii;
use Couchbase\N1qlQuery;
use yii\helpers\ArrayHelper;
use matrozov\couchbase\ActiveRecord;
use asimet\red\models\Partner;

/**
 * Description of OrdenCompra
 *
 * @author gilles
 */
class Cotizacion extends ActiveRecord {

    // Estado Cotizacion
    const ESTADO_VIGENTE = "1";
    const ESTADO_ACEPTADA = "2";
    const ESTADO_RECHAZADA = "3";
    const ESTADO_ACEPTADA_PARTIAL = "4";
    // Estado Item
    const ESTADO_ITEM_PENDIENTE = "1";
    const ESTADO_ITEM_ACEPTADO = "2";
    const ESTADO_ITEM_RECHAZAD0 = "3";

    public $valorIVA;
    public $valorTotal;
    public $itemList;

    /**
     * @return array list of attribute names.
     */
    public function attributes() {
        return ['_id', 'fecha_ingreso', 'fecha_fin_validez', 'valor_neto_total', 'estado_cotizacion', 'proveedor', 'proveedor_uuid', 'comentario', 'items', 'identificador'];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            '_id' => 'ID',
            'contacto_empresa_cliente_id' => 'Contacto Empresa Cliente',
            'user.username' => 'usuario',
            'user_id' => 'User ID',
            'fecha_ingreso' => 'Fecha Ingreso',
            'valor_neto_total' => 'Valor Neto',
            'fecha_fin_validez' => 'Fecha Fin Validez',
            'estado_cotizacion' => 'Estado Cotización',
            'departamento' => 'Empresa Cliente',
            'contactoEmpresaCliente.nombre' => 'Cliente',
            'comentario' => 'Observaciones',
            'partner.nombre' => 'Proveedor',
            'identificador' => 'ID'
        ];
    }

    /**
     * 
     * @return Array
     */
    public function getEstadoOption() {
        return [
            self::ESTADO_VIGENTE => 'Vigente',
            self::ESTADO_ACEPTADA => 'Aceptada',
            self::ESTADO_RECHAZADA => 'Rechazada',
            self::ESTADO_ACEPTADA_PARTIAL => 'Aceptada parcialmente'
        ];
    }

    /**
     * 
     * @return Array
     */
    public function getEstadoItemOption() {
        return [
            self::ESTADO_ITEM_PENDIENTE => 'Pendiente',
            self::ESTADO_ITEM_ACEPTADO => 'Aceptado',
            self::ESTADO_ITEM_RECHAZAD0 => 'Rechazado'
        ];
    }

    public function getIva() {
        return (intval($this->valor_neto_total) * (19 / 100));
    }

    public function getTotal() {
        return ($this->valor_neto_total + $this->iva);
    }

    public function getPartner() {
        return Partner::findOne($this->proveedor_uuid);
    }

    public static function getCotizacions($proveedor_uuid, $estados = []) {

        $ct = [];
        $cotizacions = [];
        $sql = 'SELECT META().id FROM `cotizacion` cot WHERE cot.proveedor_uuid = "' . $proveedor_uuid . '"';

        if (!empty($estados)) {
            $sql .= 'AND (';
            foreach ($estados as $estado) {
                $ct[] = 'cot.estado_cotizacion = "' . $estado . '"';
            }
            $sql .= implode(' OR ', $ct) . ')';
        }

        $n1ql = N1qlQuery::fromString($sql);
        $n1ql->consistency(N1qlQuery::REQUEST_PLUS);

        $result = Yii::$app->couchbase->getBucket('cotizacion')->bucket->query($n1ql, true);

        if ($result->status === 'success' && !empty($result->rows)) {
            foreach ($result->rows as $row) {
                $cotizacions[] = $row['id'];
            }
        }

        return $cotizacions;
    }

    public function afterFind() {

        parent::afterFind();

        $this->valorIVA = $this->getIva();
        $this->valorTotal = $this->getTotal();

        if (!empty($this->items)) {
            foreach ($this->items as $item) {
                $this->itemList[] = [
                    'item' => $item["item"],
                    'estado' => (empty($item["estado"])) ? self::ESTADO_ITEM_PENDIENTE : $item["estado"],
                    'descripcion' => $item["descripcion"],
                    'fecha_entrega' => ((!empty($item["fecha_entrega"])) ? Yii::$app->formatter->asDate($item["fecha_entrega"]) : ''),
                    'cantidad' => $item["cantidad"],
                    'precio' => $item["precio"],
                    'total' => $item["total"],
                ];
            }
        }
    }

    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {
            if ($insert) {
                $identificador = Yii::$app->couchbase->getBucket('cotizacion')->bucket->counter('cotizacion_id', 1, ['initial' => 100]);
                $this->identificador = $identificador->value;
            }

            return true;
        }

        return false;
    }

    public function changeEstadoItem($state, $itemToChange = null) {

        if (!empty($this->items)) {

            $result = [];

            foreach ($this->items as $item) {
                if (empty($itemToChange)) {
                    $item["estado"] = $state;
                } else {
                    if ($itemToChange == $item["item"]) {
                        $item["estado"] = $state;
                    }
                }
                $result[] = $item;
            }

            $this->items = $result;
        }
    }

    public function getItemOptionList($estados = []) {

        $result = [];

        foreach ($this->items as $item) {
            if (empty($estados) || (!empty($estados) && in_array($item['estado'], $estados))) {
                $result[$item['item']] = $item['descripcion'];
            }
        }

        return $result;
    }

    public function getItemOption($estados = []) {

        $result = [];

        foreach ($this->items as $item) {
            if (empty($estados) || (!empty($estados) && in_array($item['estado'], $estados))) {
                $result[] = ["id" => $item['item'], "name" => $item['descripcion']];
            }
        }

        return $result;
    }

    /**
     * 
     * @return Array
     */
    public function getColorOption() {
        return [
            self::ESTADO_VIGENTE => 'GreenYellow',
            self::ESTADO_ACEPTADA => 'DarkTurquoise',
            self::ESTADO_RECHAZADA => 'Yellow'
        ];
    }

    public function getEstadoText() {
        $options = $this->estadoOption;
        return (isset($options[$this->estado_cotizacion])) ? $options[$this->estado_cotizacion] : "Desconocido";
    }

    public function getColorText() {
        $options = $this->colorOption;
        return (isset($options[$this->estado_cotizacion])) ? $options[$this->estado_cotizacion] : "white";
    }

    public function getTextCotizacion() {
        return $this->identificador . ' - ' . $this->partner->nombre;
    }

    /**
     * Generates the data suitable for list-based HTML elements
     * @return yii\helpers\ArrayHelper
     */
    public static function getOptionList() {
        $cotizaciones = Cotizacion::find()->where(["estado_cotizacion" => self::ESTADO_ACEPTADA])->orWhere(["estado_cotizacion" => self::ESTADO_ACEPTADA_PARTIAL])->all();
        return ArrayHelper::map($cotizaciones, '_id', 'textCotizacion');
    }

}
