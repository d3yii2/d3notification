<?php


namespace d3yii2\d3notification\interfaces;


use d3yii2\d3notification\models\D3nNotification;

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
     * @param D3nNotification $notification
     */
    public function loadNotificationData(D3nNotification $notification): void;

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

    public function getNotificationStatusId(): int;
    public function getNotificationTypeId(): int;

    /**
     *
     * @return array ['label1' = [''url1],'label12 = [''url3],]
     */
    public function getNotificationLinkList(): array;
}