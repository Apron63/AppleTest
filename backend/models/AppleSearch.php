<?php

namespace app\models;

use yii\data\ActiveDataProvider;

class AppleSearch extends Apple
{
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Apple::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->andFilterWhere(['!=', 'status', Apple::STATUS_DESTROYED_SUCCESSFUL]);

        return $dataProvider;
    }
}