<?php


namespace d3yii2\d3notification\logic;


use d3yii2\d3notification\models\D3nNotification;

class DashboardLogic
{
    /**
     * @var int
     */
    private $sys_company_id;

    /**
     * DashboardLogic constructor.
     * @param $sys_company_id
     */
    public function __construct(int $sys_company_id)
    {
        $this->sys_company_id = $sys_company_id;
    }

    /**
     * @param int[] $statusIdList
     * @return array
     */
    public function getList(array $statusIdList): array
    {
        return D3nNotification::find()
            ->select([
                'type_id',
                'cnt' => 'COUNT(*)',
                'status_id',
                'minTime' => 'MIN(`time`)',
                'maxTime' => 'MAX(`time`)',
            ])
            ->where([
                'sys_company_id' => $this->sys_company_id,
                'status_id' => $statusIdList
            ])
            ->groupBy([
                'type_id',
                'status_id'
            ])
            ->asArray()
            ->all();
    }
}