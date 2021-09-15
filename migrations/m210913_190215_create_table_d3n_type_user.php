<?php

use yii\db\Migration;

/**
 * Class m210913_190215_create_table_d3n_type_user*/
class m210913_190215_create_table_d3n_type_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            create table d3n_type_user
            (
             user_id int unsigned not null,
             type_id smallint unsigned not null,
             id smallint unsigned auto_increment primary key,
             alert_type enum('email') default 'email' null,
             constraint d3n_type_user_ibfk_type
              foreign key (type_id) references d3n_type (id)
            )
            charset=latin1
        ");
        $this->execute("create index type_id on d3n_type_user (type_id)");
    }
    
    public function safeDown()
    {
        echo "m210913_190215_create_table_d3n_type_user cannot be reverted.\n";
        return false;
    }
    
}