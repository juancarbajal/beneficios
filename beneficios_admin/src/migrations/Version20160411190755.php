<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160411190755 extends AbstractMigration
{
    public static $description = "Create jobs table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE IF NOT EXISTS `jobs` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
              `attempts` tinyint(3) unsigned NOT NULL,
              `reserved` tinyint(3) unsigned NOT NULL,
              `reserved_at` int(10) unsigned DEFAULT NULL,
              `available_at` int(10) unsigned NOT NULL,
              `created_at` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`),
              KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
