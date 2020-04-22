<?php

namespace d3yii2\d3notification\models;

use d3system\behaviors\D3DateTimeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use eaBlankonThema\widget\ThRmGridView;


/**
* D3nNotificationSearch represents the model behind the search form about `d3yii2\d3notification\models\D3nNotification`.
*/
class D3nNotificationSearch extends D3nNotification
{

    public function behaviors(): array
    {
        return D3DateTimeBehavior::getConfig(['time']);
    }


    /**
    * @inheritdoc
    */
    public function rules(): array
{
    return [
        [['id', 'sys_company_id', 'sys_model_id', 'model_record_id', 'key', 'type_id', 'status_id'], 'integer'],
        [['time', 'data','time_local'], 'safe'],
    ];
    }

    public function attributeLabels()
    {
         return array_merge(parent::attributeLabels(),[]);
    }

    /**
    * @inheritdoc
    */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
    * Creates data provider instance with search query applied
    *
    * @return ActiveDataProvider
    */
    public function search(): ActiveDataProvider
    {
        $query = self::find();
        $this->load(ThRmGridView::getMergedFilterStateParams());

        if (!$this->validate()) {
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        }


        $query
            ->select([
                'd3n_notification.*'
            ])
            ->andFilterWhere([
                'd3n_notification.id' => $this->id,
                'd3n_notification.sys_company_id' => Yii::$app->SysCmp->getActiveCompanyId(),
                'd3n_notification.sys_model_id' => $this->sys_model_id,
                'd3n_notification.model_record_id' => $this->model_record_id,
                'd3n_notification.key' => $this->key,
                'd3n_notification.type_id' => $this->type_id,
                'd3n_notification.status_id' => $this->status_id,
            ])
            ->andFilterWhere(['like', 'd3n_notification.data', $this->data])
            ->andFilterWhereDateRange('d3n_notification.time', $this->time);
        return new ActiveDataProvider([
            'query' => $query,
            //'sort' => ['defaultOrder' => ['????' => SORT_ASC]]
            'pagination' => [
                'params' => ThRmGridView::getMergedFilterStateParams(),
            ],
            'sort' => [
                'params' => ThRmGridView::getMergedFilterStateParams(),
            ],
        ]);
    }
}