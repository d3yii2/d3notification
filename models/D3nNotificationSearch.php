<?php

namespace d3yii2\d3notification\models;

use d3system\behaviors\D3DateTimeBehavior;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use eaBlankonThema\widget\ThRmGridView;
use yii\db\ActiveQuery;
use yii\db\Exception;

/**
 * D3nNotificationSearch represents the model behind the search form about `d3yii2\d3notification\models\D3nNotification`.
 */
class D3nNotificationSearch extends D3nNotification
{
    public $userId;

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
            [['id', 'sys_company_id', 'sys_model_id', 'model_record_id', 'key', 'type_id', 'userId'], 'integer'],
            [['time', 'data', 'time_local', 'status_id', 'userId'], 'safe'],
        ];
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
     * @throws Exception|\yii\base\InvalidConfigException
     */
    public function search(): ActiveDataProvider
    {
        $this->load(ThRmGridView::getMergedFilterStateParams());

        if (!$this->validate()) {
            return new ActiveDataProvider([
                'query' => self::find()->where('1=2'),
            ]);
        }

        return new ActiveDataProvider([
            'query' => $this->getQuery(),
            //'sort' => ['defaultOrder' => ['????' => SORT_ASC]]
            'pagination' => [
                'params' => ThRmGridView::getMergedFilterStateParams(),
            ],
            'sort' => [
                'params' => ThRmGridView::getMergedFilterStateParams(),
            ],
        ]);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param int $sysModelId
     * @param int $modelRecordId
     * @return ActiveDataProvider
     * @throws Exception|\yii\base\InvalidConfigException
     */
    public function searchForRecord(int $sysModelId, int $modelRecordId): ActiveDataProvider
    {
        $this->sys_model_id = $sysModelId;
        $this->model_record_id = $modelRecordId;


        return new ActiveDataProvider([
            'query' => $this->getQuery(),
            'pagination' => false,
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws Exception|\yii\base\InvalidConfigException
     */
    public function getQuery(): ActiveQuery
    {
        return self::find()
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
            ->andFilterWhereDateRange('d3n_notification.time', $this->time)
            ->groupBy('d3n_notification.id');
    }
}
