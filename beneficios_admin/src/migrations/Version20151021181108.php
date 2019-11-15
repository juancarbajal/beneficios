<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151021181108 extends AbstractMigration
{
    public static $description = "Add field Principal BNF_Imagen table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE  `BNF_Imagen` ADD  `Principal` ENUM(  '0',  '1' ) NOT NULL AFTER  `Nombre` ;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
