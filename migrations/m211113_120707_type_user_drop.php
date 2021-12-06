<?php

use yii\db\Migration;

class m211113_120707_type_user_drop  extends Migration {

    public function safeUp() { 
        $this->execute('
            DROP TABLE IF EXISTS d3n_type_user;        
        ');
    }

    public function safeDown() {
        echo "m211113_120707_type_user_drop cannot be reverted.\n";
        return false;
    }
}
