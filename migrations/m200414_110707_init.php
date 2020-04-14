<?php

use yii\db\Migration;

class m200414_110707_init  extends Migration {

    public function safeUp() {

        $this->execute('
            CREATE TABLE `d3n_status` (
              `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
              `sys_model_id` tinyint(3) unsigned NOT NULL,
              `status_id` tinyint(3) unsigned NOT NULL,
              `label` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
              KEY `id` (`id`),
              KEY `sys_model_id` (`sys_model_id`),
              CONSTRAINT `d3n_status_ibfk_1` FOREIGN KEY (`sys_model_id`) REFERENCES `sys_models` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
        ');

        $this->execute('
            CREATE TABLE `d3n_notification` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `sys_company_id` smallint(5) unsigned NOT NULL,
              `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `sys_model_id` tinyint(3) unsigned NOT NULL,
              `model_record_id` int(10) unsigned NOT NULL,
              `key` int(10) unsigned NOT NULL,
              `status_id` smallint(5) unsigned NOT NULL,
              `data` text,
              PRIMARY KEY (`id`),
              KEY `sys_model_id` (`sys_model_id`),
              KEY `status_id` (`status_id`),
              KEY `sys_company_id` (`sys_company_id`),
              CONSTRAINT `d3n_notification_ibfk_1` FOREIGN KEY (`sys_model_id`) REFERENCES `sys_models` (`id`),
              CONSTRAINT `d3n_notification_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `d3n_status` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1

        ');

        $this->execute('
            CREATE TABLE `d3n_status_history` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `notification_id` int(10) unsigned NOT NULL,
              `status_id` smallint(5) unsigned NOT NULL,
              `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              `user_id` int(10) unsigned DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `notification_id` (`notification_id`),
              KEY `status_id` (`status_id`),
              CONSTRAINT `d3n_status_history_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `d3n_notification` (`id`),
              CONSTRAINT `d3n_status_history_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `d3n_status` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
        ');
    }

    public function safeDown() {
        $this->execute('DROP TABLE `d3n_status_history`;');
        $this->execute('DROP TABLE `d3n_notification`;');
        $this->execute('DROP TABLE `d3n_status`;');
    }
}
