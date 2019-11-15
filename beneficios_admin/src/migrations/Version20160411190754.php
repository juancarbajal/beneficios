<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160411190754 extends AbstractMigration
{
    public static $description = "Create failed_jobs table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE IF NOT EXISTS `failed_jobs` (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `connection` text COLLATE utf8_unicode_ci NOT NULL,
          `queue` text COLLATE utf8_unicode_ci NOT NULL,
          `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
          `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
