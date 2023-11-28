<?php

use yii\db\Migration;

class m200414_110707_init  extends Migration {

    public function safeUp() {

        $this->execute('DROP TABLE IF EXISTS d3n_status_history');
        $this->execute('DROP TABLE IF EXISTS d3n_notification');
        $this->execute('DROP TABLE IF EXISTS d3n_status');
        $this->execute('DROP TABLE IF EXISTS d3na_type_person');
        $this->execute('DROP TABLE IF EXISTS d3n_type');
        $this->execute('
            CREATE TABLE `d3n_status` (
              `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
              `sys_model_id` tinyint(3) unsigned NOT NULL,
              `status_id` tinyint(3) unsigned NOT NULL,
              `label` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `id` (`id`),
              KEY `sys_model_id` (`sys_model_id`),
              CONSTRAINT `d3n_status_ibfk_1` FOREIGN KEY (`sys_model_id`) REFERENCES `sys_models` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
        ');

        $this->execute('
            CREATE TABLE `d3n_type` (
              `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
              `sys_model_id` tinyint(3) unsigned NOT NULL,
              `type_id` tinyint(3) unsigned NOT NULL,
              `label` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `id` (`id`),
              KEY `sys_model_id` (`sys_model_id`),
              CONSTRAINT `d3n_type_ibfk_sys_model` FOREIGN KEY (`sys_model_id`) REFERENCES `sys_models` (`id`)
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
              `type_id` smallint(5) unsigned,
              `data` text,
              PRIMARY KEY (`id`),
              KEY `sys_model_id` (`sys_model_id`),
              KEY `status_id` (`status_id`),
              KEY `sys_company_id` (`sys_company_id`),
              CONSTRAINT `d3n_notification_ibfk_sys_model` FOREIGN KEY (`sys_model_id`) REFERENCES `sys_models` (`id`),
              CONSTRAINT `d3n_notification_ibfk_status` FOREIGN KEY (`status_id`) REFERENCES `d3n_status` (`id`),
              CONSTRAINT `d3n_notification_ibfk_type` FOREIGN KEY (`type_id`) REFERENCES `d3n_type` (`id`)
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
              CONSTRAINT `d3n_status_history_ibfk_notification` FOREIGN KEY (`notification_id`) REFERENCES `d3n_notification` (`id`),
              CONSTRAINT `d3n_status_history_ibfk_status` FOREIGN KEY (`status_id`) REFERENCES `d3n_status` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
        ');
    }

    public function safeDown() {
        $this->execute('DROP TABLE `d3n_status_history`;');
        $this->execute('DROP TABLE `d3n_notification`;');
        $this->execute('DROP TABLE `d3n_status`;');
        $this->execute('DROP TABLE `d3n_type`;');
    }
}
