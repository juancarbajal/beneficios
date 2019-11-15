<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150918185500 extends AbstractMigration
{
    public static $description = "Alter BNF_BolsaTotal table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_BolsaTotal`
            DROP COLUMN `id`,
            DROP PRIMARY KEY,
            ADD PRIMARY KEY (`BNF_TipoPaquete_id`, `BNF_Empresa_id`)  COMMENT '';"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
