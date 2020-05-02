<?php


namespace d3yii2\d3notification\interfaces;


interface Notification
{

    /**
     * get active record primary key value
     * @return int
     */
    public function getNotificationRecordId(): int;

    /**
     * get additional key
     * @return int
     */
    public function getNotificationKey(): int;

    /**
     * load notification data
     * @param array $data
     */
    public function loadNotificationData(array $data): void;

    /**
     * get notification data
     * @return array
     */
    public function getNotificationData(): array;

    /**
     * Return status list
     *
     * @return string[]
     */
    public function getNotificationStatusList(): array;

    /**
     * Return type list
     *
     * @return string[]
     */
    public function getNotificationTypeList(): array;

    /**
     * Actual status
     * @return int
     */
    public function getStatusId(): int;

    /**
     * Get type
     * @return int
     */
    public function getTypeId(): int;

    /**
     *
     * @return array ['label1' = [''url1],'label12 = [''url3],]
     */
    public function getLinkList(): array;
}