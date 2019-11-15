<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151001095218 extends AbstractMigration
{
    public static $description = "Update BNF_Pais table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("UPDATE `BNF_Pais` SET `NombrePais` = 'Perú' WHERE `id` = 1;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
