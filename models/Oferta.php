<?php

namespace asimet\red\models;

use Yii;
use yii\helpers\Url;
use Couchbase\N1qlQuery;
use matrozov\couchbase\ActiveRecord;
use yii\helpers\ArrayHelper;
use asimet\red\models\Partner;
use DateTime;

/**
 * This is the model class for table "oferta".
 *
 * @property integer $id
 * @property string $fecha_cierre
 * @property string $fecha_entrega
 * @property integer $estado
 * @property string $detalle
 */
class Oferta extends ActiveRecord {

    const PRIVATE_KEY = '-----BEGIN RSA PRIVATE KEY-----' . "\n"
            . 'Proc-Type: 4,ENCRYPTED' . "\n"
            . 'DEK-Info: AES-128-CBC,8D1CD714CC1CA496B6BDAAC20B5E4030' . "\n"
            . '' . "\n"
            . 'eZNKi+rJe/OfTgKnkmLGZQYfUhPXDFM0GCvq6eOUnkXoooPt7i+45+jpDWdZMPpZ' . "\n"
            . 'cFVl9CYCZzeRPzByb0+fb6YKjB/W/VwLkd4OBUWRZfkctjSmqQeSrpn4R6T3NAWn' . "\n"
            . 'Tye26nBvDCHBStUpkDaYtWQBEpo0+UhcquVd70OyimT2mNPPX3FY3LMNnSdxuoTa' . "\n"
            . 'qQszAw+YYdl/7Y/T2IQ7BPnzooaSz/pNxsxomghzJa6TBsrrJ/YPbsM4Jgcs85d9' . "\n"
            . 'WeJbtmLSSSU3pPBPl2fE62GWHpwEV+79oy8SNZDUOMReNVYr7uVwho0IctsE9mFn' . "\n"
            . 'ceS8i5f59N2x1vPkPD11Ntv4YA50AE9A4zWemT8zeIDhIcvA1mJ5fmimWbmSb36f' . "\n"
            . 'llewyAf0gDN481y2pzfczg6Fx5+URSexifDWVLH2aB2q+Z34ejQNqnUorG848b5D' . "\n"
            . 'f2h3yPcybWCDMxoNcKzKGWcVh6gLxVQtaKwUnO8fXXdJarBurKyWQn28m9e7bWTU' . "\n"
            . 'qwB16DFIhvNsX4Wp8wByqBxjVN4JzN98GC2OUnsFD6cE4lRg4ruGlvbKEWEHmWrk' . "\n"
            . 'z/fxKPwVhLARYGkWSQ2zGl0g+4s1enoKOjRlZw23dfz1v2Umyk6VJnDmFVmnr5Np' . "\n"
            . 'gKEDcSsmcTjtrADjQOwpifF79qb+cbBhhmD1zxCwshLggNKkjJGVlBplNEFWRFcT' . "\n"
            . 'LiFMbbxpnmL2RIUGg1zaNBwAOMoPNbBdBiyi4FnzcLgt11F/jBcIrwimUCjBw+W6' . "\n"
            . 'almKY4L95liRdgFLvYRnbWY3NIUyY7JLu6OpUC61vvy1SigoKdO+iARTU4iw1VsC' . "\n"
            . '3ILgVqRxN8frqFw0lVosehSanA4JYgS8f7FreJbSyl0nOF5dPWOp9fhgwr+3th9q' . "\n"
            . 'bFyklpWBLp1uTTQk4NUeusNm0IQu8Huk4HR0acgTcAPbpGjS/6OMOm1Kn1A91fWD' . "\n"
            . 'pwfshkONWNUxwL0sxEWfI+GBo/Pj8+ZXQL8A3dR4leuGbpxMAOUwCscuM7EtaWbU' . "\n"
            . 'nAtbX5un7U+o0sVY1n7bL3BWXWzObawXVC1QkLl3ecmLZBhlHnUbOLCZRdPlcUwK' . "\n"
            . 'hOHgzGtkCcj2k12F+G1XKsUmX7MJdqEnZtrcTy/pqznPeYfnYvCdh7kZRSOWy9/V' . "\n"
            . 'hc3kuvx+J6UPz60iY41K/B5Gjs/dRCDEqBiaKtBJWa9Mimkgv4j3NHc4hTpqdzOY' . "\n"
            . 'papo6itxXeGTDqgni0dnMSdo2lPaA7UR3QPrrItpEG2HlXTX9IMDMMZ1qCNpww3r' . "\n"
            . 'L7sezQGv4haTBIwntTyQQiiplfyD9VDOJ2fx0fvG8U/1dNC+ZgBB8aSAJqqQnWD+' . "\n"
            . 'S+0kMW0euYMSwFigQ0WEt7IuRzpRwy//zkjibD4hTHRr+kijd9xol31RG6XyKEw/' . "\n"
            . 'IdEDIxb0q4LYwneTv71vKSa8K7cILhuUfE+YRImmy7WwGcPXNQ4bO6GiIeH5L92u' . "\n"
            . '7tAgkoxaH9S+nrd6G1BP/yjq1576FXYMq/Ms6JS7R3KyVXhYipldUHXN+2Q9jqWA' . "\n"
            . 'Fn4eD6+4Xuob9rH1Pv+X+5p4cNwXKmsgi26tzSaix9dUYn2OWC8CTPkw54x8TW92' . "\n"
            . '-----END RSA PRIVATE KEY-----' . "\n";

    public $proveedorList;
    public $cotizacionList;
    public $itemList;
    public $archives;

    // Estado
    const ESTADO_CERRADA = "1";
    const ESTADO_ASIGNADA = "2";
    const ESTADO_ABIERTA = "3";
    // Session name
    const ARCHIVES_UPLOADS = "uploadsOferta";

    /**
     * @return array list of attribute names.
     */
    public function attributes() {
        return ['_id', 'fecha_ingreso', 'fecha_actualizacion', 'fecha_cierre', 'fecha_entrega', 'estado', 'detalle', 'items', 'proveedores', 'cliente', 'cotizaciones', 'archivos', 'identificador', 'code'];
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['fecha_cierre', 'fecha_entrega', 'estado'], 'required'],
            [['fecha_cierre', 'fecha_entrega', 'itemList', 'proveedorList', 'archives', 'code'], 'safe'],
            [['estado'], 'integer'],
            [['detalle'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            '_id' => 'ID',
            'fecha_ingreso' => 'Fecha Ingreso',
            'fecha_cierre' => 'Fecha Cierre',
            'fecha_entrega' => 'Fecha Entrega',
            'estado' => 'Estado',
            'estadoText' => 'Estado',
            'detalle' => 'Detalle',
            'owner.nombre' => 'Empresas Clientes',
            'code' => 'Código interno de solicitud de cotización'
        ];
    }

    /**
     * 
     * @return Array
     */
    public function getEstadoOption() {
        return [
            self::ESTADO_CERRADA => 'Cerrada',
            self::ESTADO_ASIGNADA => 'Asignada',
            self::ESTADO_ABIERTA => 'Abierta',
        ];
    }

    /**
     * 
     * @return Array
     */
    public function getColorOption() {
        return [
            self::ESTADO_ABIERTA => 'GreenYellow',
            self::ESTADO_ASIGNADA => 'DarkTurquoise',
            self::ESTADO_CERRADA => 'Grey'
        ];
    }

    public function getEstadoText() {
        $options = $this->estadoOption;
        return (isset($options[$this->estado])) ? $options[$this->estado] : "Desconocido";
    }

    public function getColorText() {
        $options = $this->colorOption;
        return (isset($options[$this->estado])) ? $options[$this->estado] : "white";
    }

    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {

            $items = [];

            if (!empty($this->itemList)) {
                foreach ($this->itemList as $item) {
                    $fecha_entrega = DateTime::createFromFormat("d/m/Y", $item["fecha_entrega"]);
                    $items[] = [
                        "cantidad" => $item["cantidad"],
                        "descripcion" => $item["descripcion"],
                        "fecha_entrega" => (empty($fecha_entrega) ? '' : $fecha_entrega->format("Y-m-d")),
                    ];
                }
            }

            $this->items = $items;

            if (!empty($this->proveedorList)) {
                if (isset($this->proveedorList["proveedor"])) {
                    $this->proveedores = $this->proveedorList["proveedor"];
                }
            }

            if (!Yii::$app->request->isConsoleRequest) {

                $session = Yii::$app->session;

                if ($session->has(self::ARCHIVES_UPLOADS)) {
                    $this->archivos = array_values($session[self::ARCHIVES_UPLOADS]);
                    $session->remove(self::ARCHIVES_UPLOADS);
                }
            }

            if ($insert) {

                $this->fecha_ingreso = (new \DateTime("now", new \DateTimeZone("America/Santiago")))->format("Y-m-d H:i:s");
                $identificador = Yii::$app->couchbase->getBucket('oferta')->bucket->counter('oferta_id', 1, ['initial' => 100]);
                $this->identificador = $identificador->value;
            } else {
                $this->fecha_actualizacion = (new \DateTime("now", new \DateTimeZone("America/Santiago")))->format("Y-m-d H:i:s");
            }

            return true;
        }

        return false;
    }

    public function afterFind() {

        parent::afterFind();

        if (!empty($this->items)) {
            foreach ($this->items as $item) {
                $this->itemList[] = [
                    "cantidad" => $item["cantidad"],
                    "descripcion" => $item["descripcion"],
                    "fecha_entrega" => ((!empty($item["fecha_entrega"])) ? Yii::$app->formatter->asDate($item["fecha_entrega"]) : ''),
                ];
            }
        }

        if (!empty($this->proveedores)) {
            foreach ($this->proveedores as $proveedor) {
                $this->proveedorList[] = [
                    "proveedor" => $proveedor,
                ];
            }
        }

        if (!empty($this->cotizaciones)) {
            foreach ($this->cotizaciones as $cotizacion) {
                $this->cotizacionList[] = [
                    "proveedor" => $cotizacion["proveedor"],
                    "precio" => $cotizacion["precio"],
                ];
            }
        }
    }

    public static function getEmpresaRedOption() {

        $proveedors = [];

        $sql = 'SELECT META().id, nombre FROM `red-asimet` ra WHERE ra.activo = "1" AND ANY r IN reglas SATISFIES r.tipo = "cliente" END;'; // TODO ver para acepta subquery
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

    public function getOwner() {
        return Partner::FindOne($this->cliente);
    }

    public function isOwner() {
        return (Yii::$app->getModule('ared')->uuid == $this->cliente);
    }

    public function haveCotizacion() {

        $uuid = Yii::$app->getModule('ared')->uuid;

        if (!empty($this->cotizaciones)) {
            $proveedor = ArrayHelper::getColumn($this->cotizaciones, 'proveedor');
            return array_search($uuid, $proveedor);
        }

        return false;
    }

    public static function addArchiveSession($key, $archive) {

        $session = Yii::$app->session;
        $uploads = [];

        if ($session->has(self::ARCHIVES_UPLOADS)) {
            $uploads = $session[Oferta::ARCHIVES_UPLOADS];
            if (!array_key_exists($key, $uploads)) {
                $uploads[$key] = $archive;
                $session[Oferta::ARCHIVES_UPLOADS] = $uploads;
            }
        } else {
            $session[Oferta::ARCHIVES_UPLOADS] = [];
            $uploads[$key] = $archive;
            $session[Oferta::ARCHIVES_UPLOADS] = $uploads;
        }
    }

    public static function addArchivesSession($archives) {

        foreach ($archives as $key => $archive) {
            Oferta::addArchiveSession($key, $archive);
        }
    }

    public static function getType($extension) {

        $ext = strtolower($extension);

        if ($extension === "pdf") {
            return "pdf";
        } elseif (in_array($ext, ["xls", "doc", "ppt", "pptx"])) {
            return "office";
        } elseif (in_array($ext, ["png", "jpg", "jpeg", "ico", "gif"])) {
            return "image";
        } elseif (in_array($ext, ["csv", "txt", "log", "sql", "md"])) {
            return "text";
        } elseif ($extension === "html") {
            return "html";
        } else {
            return 'object';
        }
    }

    public function getUploads($info) {

        if (!empty($this->archivos)) {

            $documentos = [];
            $archives = [];

            foreach ($this->archivos as $archivo) {

                if ($info == 'link') {
                    $documentos[] = $archivo["downloadUrl"];
                }
                if ($info == 'config') {
                    $documentos[] = [
                        'type' => $archivo["type"],
                        'caption' => $archivo["caption"],
                        'size' => $archivo["size"],
                        'url' => Url::to(['/ared/oferta/delete-upload', "id" => $archivo["key"]]),
                        'key' => $archivo["key"],
                    ];

                    $archives[$archivo["key"]] = $archivo;
                }
            }

            Oferta::addArchivesSession($archives);
            return $documentos;
        }

        return false;
    }

}
