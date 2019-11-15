<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170214113126 extends AbstractMigration
{
    public static $description = "Create BNF_Cliente_Landing table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF_Cliente_Landing` (
          `id` INT NOT NULL AUTO_INCREMENT,
          `BNF_TipoDocumento_id` INT(11) NULL,
          `NumeroDocumento` VARCHAR(15) NULL,
          `Nombre` VARCHAR(60) NULL,
          `Apellido` VARCHAR(60) NULL,
          `Telefono` VARCHAR(10) NULL,
          `Email` VARCHAR(60) NULL,
          `FechaCreacion` DATETIME NULL DEFAULT NULL,
          `FechaActualizacion` DATETIME NULL DEFAULT NULL,
          PRIMARY KEY (`id`),
          INDEX `fk_BNF_Cliente_Landing_1_idx` (`BNF_TipoDocumento_id` ASC),
          CONSTRAINT `fk_BNF_Cliente_Landing_1`
            FOREIGN KEY (`BNF_TipoDocumento_id`)
            REFERENCES `BNF_TipoDocumento` (`id`)
            ON DELETE CASCADE
            ON UPDATE NO ACTION);");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
