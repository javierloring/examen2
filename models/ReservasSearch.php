<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ReservasSearch represents the model behind the search form of `app\models\Reservas`.
 */
class ReservasSearch extends Reservas
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'usuario_id', 'vuelo_id'], 'integer'],
            [['asiento'], 'number'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Reservas::find()
        ->leftJoin('vuelos', 'reservas.vuelo_id = vuelos.id')
        ->where(['usuario_id' => Yii::$app->user->id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $dataProvider->sort->attributes['vuelo.codigo'] = [
            'asc' => ['codigo' => SORT_ASC],
            'desc' => ['codigo' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            'reservas.id' => $this->id,
            'usuario_id' => $this->usuario_id,
            'vuelo_id' => $this->vuelo_id,
            'asiento' => $this->asiento,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }
}
