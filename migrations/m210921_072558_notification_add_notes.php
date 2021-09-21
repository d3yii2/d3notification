<?php

use yii\db\Migration;

/**
* Class m210921_072558_notification_add_notes*/
class m210921_072558_notification_add_notes extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute('alter table d3n_notification add notes text null');
    }

    public function safeDown()
    {
        echo "m210921_072558_notification_add_notes cannot be reverted.\n";
        return false;
    }

}