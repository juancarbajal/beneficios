<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718151005 extends AbstractMigration
{
    public static $description = "Alter BNF2_Campanias table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Campanias`
            CHANGE COLUMN `EstadoCampania` `EstadoCampania` 
            ENUM('Borrador', 'Publicado', 'Eliminado', 'Caducado') CHARACTER SET 'utf8' NOT NULL ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
