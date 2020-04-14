<?php


namespace d3yii2\d3notification\interfaces;


interface Notification
{
    /**
     * return actual status
     * @return int
     */
    public function getNotificationStatus() : int;

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
     * get status
     * @return int
     */
    public function getNotificationStatusId() : int;

    /**
     * get notification data
     * @return array
     */
    public function getNotificationData() : array;

    /**
     * actual status label
     * @return string
     */
    public function getNotificationStatusLabel() : string;
}