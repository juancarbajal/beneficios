<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160717140135 extends AbstractMigration
{
    public static $description = "Alter BNF2_Demanda table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Demanda` 
             DROP FOREIGN KEY `fk_BNF2_Demanda_2`;
             ALTER TABLE `BNF2_Demanda` 
             DROP COLUMN `BNF2_Segmento_id`,
             DROP INDEX `fk_BNF2_Demanda_2_idx` ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
