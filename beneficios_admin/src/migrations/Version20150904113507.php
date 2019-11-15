<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150904113507 extends AbstractMigration
{
    public static $description = "Delete fields [CostoPorLead,BNF_Usuario_id] BNF_Paquete table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Paquete`
          DROP FOREIGN KEY `fk_BNF_Paquete_BNF_Usuario1`;

        ALTER TABLE `BNF_Paquete`
          DROP COLUMN `CostoPorLead`,
          DROP COLUMN `BNF_Usuario_id`,
          DROP INDEX `fk_BNF_Paquete_BNF_Usuario1_idx` ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
