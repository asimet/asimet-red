<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace asimet\red\models;

use Yii;
use matrozov\couchbase\ActiveRecord;
use Couchbase\N1qlQuery;

/**
 * Description of Test
 *
 * @author gilles
 */
class Partner extends ActiveRecord {

    const TIPO_CLIENTE = "cliente";
    const TIPO_PROVEEDOR = "proveedor";

    public $rulesList = [];
    public $fileLogo;

    // Session name
    const LOGO_UPLOAD = "logoUploadPartner";

    /**
     * @return array list of attribute names.
     */
    public function attributes() {
        return ['_id', 'nombre', 'nivel', 'correo', 'direccion', 'reglas', 'identity', 'activo', 'rut', 'telefono', 'zlogo'];
    }

    public static function bucketName() {
        return "red-asimet";
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['nombre', 'correo'], 'required'],
            [['rulesList', 'reglas', 'accept', 'identity', 'activo', 'direccion', 'rut', 'telefono'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            '_id' => 'ID',
            'rut' => 'RUT',
            'direccion' => 'Dirección',
            'fileLogo' => 'Logo',
            'nombre' => 'Mi nombre en la red',
            'telefono' => 'Teléfono',
            'correo' => 'Mi correo de contacto en la red'
        ];
    }

    public function getTipoOption() {
        return [
            self::TIPO_CLIENTE => "cliente",
            self::TIPO_PROVEEDOR => "proveedor",
        ];
    }

    public function afterFind() {

        parent::afterFind();
        $partners = [];

        if (!empty($this->reglas)) {
            foreach ($this->reglas as $regla) {
                if ($regla["tipo"] == self::TIPO_PROVEEDOR) {
                    foreach ($regla["acepta"] as $accept) {
                        $partners[$accept][] = self::TIPO_PROVEEDOR;
                    }
                }
                if ($regla["tipo"] == self::TIPO_CLIENTE) {
                    foreach ($regla["acepta"] as $accept) {
                        $partners[$accept][] = self::TIPO_CLIENTE;
                    }
                }
            }
        }

        foreach ($partners as $partner => $accept) {
            $this->rulesList[] = [
                'partner' => $partner,
                'role' => $accept
            ];
        }
    }

    public function getClientes() {

        $clientes = [];

        foreach ($this->rulesList as $rule) {
            if (!empty($rule["partner"])) {
                if (in_array(self::TIPO_CLIENTE, $rule["role"])) {
                    $clientes[] = $rule["partner"];
                }
            }
        }

        return $clientes;
    }

    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {

            if ($insert) {
                $this->setAttribute("identity", Yii::$app->id);
            }

            if (!Yii::$app->request->isConsoleRequest) {

                $session = Yii::$app->session;

                if ($session->has(self::LOGO_UPLOAD)) {
                    $this->zlogo = $session[self::LOGO_UPLOAD];
                    $session->remove(self::LOGO_UPLOAD);
                }
            }

            if (!empty($this->rut)) {
                $this->rut = str_replace([',', '.'], '', $this->rut);
            }

            if (!empty($this->rulesList)) {

                $rules = [];
                $proveedores = [];
                $clientes = [];

                foreach ($this->rulesList as $rule) {
                    if (!empty($rule["partner"])) {
                        if (in_array(self::TIPO_CLIENTE, $rule["role"])) {
                            $clientes[] = $rule["partner"];
                        }
                        if (in_array(self::TIPO_PROVEEDOR, $rule["role"])) {
                            $proveedores[] = $rule["partner"];
                        }
                    }
                }

                if (!empty($clientes)) {
                    $rules[] = ["tipo" => self::TIPO_CLIENTE, "acepta" => $clientes];
                }

                if (!empty($proveedores)) {
                    $rules[] = ["tipo" => self::TIPO_PROVEEDOR, "acepta" => $proveedores];
                }

                $this->setAttribute("reglas", $rules);
            }

            return true;
        }

        return false;
    }

    public function getFormatedRut() {

        $rut = $this->rut;

        if (!empty($rut)) {
            if (($largo = strlen($rut)) == 8) {
                $rut = substr($rut, 0, 1) . '.' . substr($rut, 1, 3) . '.' . substr($rut, 4, 3) . '-' . substr($rut, 7, 1);
            } else {
                $rut = substr($rut, 0, 2) . '.' . substr($rut, 2, 3) . '.' . substr($rut, 5, 3) . '-' . substr($rut, 8, 1);
            }
        }

        return $rut;
    }

    public function getUploads($info) {

        if (!empty($this->zlogo)) {

            $documentos = [];

            $size = (int) (strlen(rtrim($this->zlogo, '=')) * 3 / 4);

            if ($info == 'link' && $size !== false) {
                $documentos[] = $this->zlogo;
            }
            if ($info == 'config' && $size !== false) {
                $documentos[] = [
                    'caption' => 'logo',
                    'size' => $size,
                    'url' => $this->zlogo,
                    'key' => '1',
                ];
            }

            return $documentos;
        }

        return false;
    }

    public static function getPartnerOption() {

        $proveedors = [];

        $availableNivel = 0;
        $nivel = Yii::$app->getModule('ared')->perfil->nivel;

        if ($nivel == "0") {
            $availableNivel = "1";
        }

        if ($nivel == "1") {
            $availableNivel = "0";
        }

        $sql = 'SELECT META().id, nombre FROM `red-asimet` ra WHERE ra.activo = "1" AND ra.nivel = "' . $availableNivel . '"';
        $n1ql = N1qlQuery::fromString($sql);
        $n1ql->consistency(N1qlQuery::REQUEST_PLUS);

        $result = Yii::$app->couchbase->getBucket('red-asimet')->bucket->query($n1ql, true);

        if ($result->status === 'success' && !empty($result->rows)) {
            foreach ($result->rows as $row) {
                $proveedors[$row['id']] = $row["nombre"];
            }
        }

        return $proveedors;
    }

}
