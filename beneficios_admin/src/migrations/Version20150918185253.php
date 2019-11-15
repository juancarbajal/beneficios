<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150918185253 extends AbstractMigration
{
    public static $description = "Alter BNF_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Oferta`
            DROP FOREIGN KEY `fk_BNF_Oferta_BNF_BolsaTotal`;
            ALTER TABLE `BNF_Oferta`
            DROP COLUMN `BNF_BolsaTotal_id`,
            DROP INDEX `fk_BNF_Oferta_BNF_BolsaTotal_idx` ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
