<?php

use yii\db\Migration;

/**
* Class m211107_212219_alter_d3n_type_user_relation*/
class m211107_212219_alter_d3n_type_user_relation extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->execute('alter table d3n_type_user modify user_id int(11) not null');
        $this->execute('alter table d3n_type_user add constraint d3n_type_user_user_id_fk foreign key (user_id) references user (id)');
    }

    public function safeDown()
    {
        return true;
        echo "m211107_212219_alter_d3n_type_user_relation cannot be reverted.\n";
        return false;
    }

}