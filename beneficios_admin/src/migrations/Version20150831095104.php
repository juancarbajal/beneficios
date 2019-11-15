<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150831095104 extends AbstractMigration
{
    public static $description = "Delete field `BNF_Empresa_id` `BNF_Cliente` table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Cliente`
            DROP FOREIGN KEY `fk_BNF_Cliente_BNF_Empresa1`;
            ALTER TABLE `BNF_Cliente`
            DROP COLUMN `BNF_Empresa_id`,
            DROP INDEX `fk_BNF_Cliente_BNF_Empresa1_idx` ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
