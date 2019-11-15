<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170216105502 extends AbstractMigration
{
    public static $description = "CREATE BNF_Configuraciones_Referidos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_Configuraciones_Referidos` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `Campo` VARCHAR(50) NULL,
              `Atributo` VARCHAR(255) NULL,
              `Tipo` ENUM('puntos', 'correo', 'imagen') NULL,
              `FechaCreacion` DATETIME NULL,
              `FechaActualizacion` DATETIME NULL,
              `Eliminado` TINYINT(1) NULL,
              PRIMARY KEY (`id`));"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
