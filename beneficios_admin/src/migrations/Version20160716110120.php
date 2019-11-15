<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716110120 extends AbstractMigration
{
    public static $description = "Alter BNF2_Oferta_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Oferta_Puntos` 
                CHANGE COLUMN `FechaVigencia` `FechaVigencia` DATE NULL ,
                CHANGE COLUMN `Stock` `Stock` INT(11) NULL ,
                CHANGE COLUMN `Slug` `Slug` VARCHAR(255) NULL ,
                CHANGE COLUMN `Estado` `Estado` ENUM('Publicado', 'Borrador', 'Caducado') NOT NULL ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
