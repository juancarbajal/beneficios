<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160126201227 extends AbstractMigration
{
    public static $description = "Alter BNF_DM_Met_Cliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_DM_Met_Cliente`
            CHANGE COLUMN `BNF_Categoria_id` `BNF_Categoria_id` VARCHAR(255) NULL DEFAULT NULL ;

            ALTER TABLE `BNF_DM_Met_Cliente`
            ADD COLUMN `DiasUltimoLogin` INT NULL AFTER `TipoOferta`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
