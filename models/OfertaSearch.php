<?php

namespace asimet\red\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use asimet\red\models\Oferta;

/**
 * OfertaSearch represents the model behind the search form about `asimet\red\models\Oferta`.
 */
class OfertaSearch extends Oferta {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['estado'], 'integer'],
            [['fecha_cierre', 'fecha_entrega', 'detalle'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function searchReceived($params) {

        $query = Oferta::find();
        $uid = Yii::$app->getModule('ared')->uuid;
        $partner = Partner::findOne($uid);

        $query->where(["estado" => parent::ESTADO_ABIERTA]);
        $query->andWhere(['in', 'cliente', $partner->clientes]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {

        $query = Oferta::find();

        $query->where(['cliente' => Yii::$app->getModule('ared')->uuid]);

        // add conditions that should always apply here

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

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->_id,
            'fecha_cierre' => $this->fecha_cierre,
            'fecha_entrega' => $this->fecha_entrega,
            'estado' => $this->estado
        ]);

        $query->andFilterWhere(['like', 'detalle', $this->detalle]);

        return $dataProvider;
    }

}
