<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160717140137 extends AbstractMigration
{
    public static $description = "Alter BNF2_Demanda_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Demanda_Log` CHANGE COLUMN `BNF2_Segmento_id` `Segmentos` TEXT NOT NULL AFTER `Rubros`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
