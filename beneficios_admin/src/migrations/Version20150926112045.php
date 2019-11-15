<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150926112045 extends AbstractMigration
{
    public static $description = "Add field Eliminado BNF_OfertaSegmento table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_OfertaSegmento`
            ADD COLUMN `Eliminado` ENUM('0', '1') NULL COMMENT '' AFTER `BNF_Segmento_id`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
