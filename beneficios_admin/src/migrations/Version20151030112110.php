<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151030112110 extends AbstractMigration
{
    public static $description = "Create BNF_Configuraciones table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_Configuraciones` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
              `Campo` VARCHAR(50) NOT NULL COMMENT '',
              `Atributo` VARCHAR(255) NOT NULL COMMENT '',
              `FechaCreacion` DATETIME NULL COMMENT '',
              `FechaActualizacion` DATETIME NULL COMMENT '',
              PRIMARY KEY (`id`)  COMMENT '',
              UNIQUE INDEX `Campo_UNIQUE` (`Campo` ASC)  COMMENT '');"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
