<?php


namespace d3yii2\d3notification\interfaces;


interface Notification
{

    /**
     * get active record primary key value
     * @return int
     */
    public function getNotificationRecordId() : int;

    /**
     * get additional key
     * @return int
     */
    public function getNotificationKey() : int;

    /**
     * get notification data
     * @return array
     */
    public function getNotificationData() : array;

    /**
     * get notification data
     * @param array $data
     */
    public function loadNotificationData(array $data): void;

    /**
     * @return array
     */
    public function getNotificationStatusList(): array;

    /**
     * @return array
     */
    public function getNotificationTypeList(): array;

    public function getNotificationStatusId(): int;
    public function getNotificationTypeId(): int;

    /**
     *
     * @return array ['label1' = [''url1],'label12 = [''url3],]
     */
    public function getNotificationLinkList(): array;
}