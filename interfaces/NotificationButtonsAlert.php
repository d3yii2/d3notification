<?php


namespace d3yii2\d3notification\interfaces;


interface NotificationButtonsAlert
{
    /**
     * change statuss
     * @param int $statusId new status
     * @param int $userId actual user
     * @return void
     */
    public function changeStatusTo(int $statusId, int $userId): void;

    /**
     * @return string
     */
    public function createAlertLabel(): string;

    /**
     * @return array
     */
    public function createButtons(): array;

    /**
     * @return string
     */
    public function createDescription(): string;
}
