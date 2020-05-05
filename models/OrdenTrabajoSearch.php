<?php

namespace asimet\red\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * OfertaSearch represents the model behind the search form about `asimet\red\models\Oferta`.
 */
class OrdenTrabajoSearch extends OrdenTrabajo {

    /**
     * @inheritdoc
     */
    public function scenarios() {
        return Model::scenarios();
    }

    public function searchCliente($params) {

        $query = OrdenTrabajo::find();

        // add conditions that should always apply here
        $query->where(['cliente_uuid' => Yii::$app->getModule('ared')->uuid]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchProveedor($params) {

        $query = OrdenTrabajo::find();

        // add conditions that should always apply here
        $query->where(['proveedor_uuid' => Yii::$app->getModule('ared')->uuid]);
        $query->andWhere(['procesado' => self::PROCESADO_PENDIENTE]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        return $dataProvider;
    }

}
