<?php

use yii\db\Migration;

class m211005_150707_status_history_add_notes  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d3n_status_history`
              CHANGE `status_id` `status_id` SMALLINT (5) UNSIGNED NULL,
              ADD COLUMN `notes` TEXT CHARSET utf8 NULL AFTER `user_id`;
            
                    
        ');
    }

    public function safeDown() {
        echo "m211005_150707_status_history_add_notes cannot be reverted.\n";
        return false;
    }
}
