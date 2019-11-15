<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151109150436 extends AbstractMigration
{
    public static $description = "Alter BNF_Cupon table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Cupon`
            CHANGE COLUMN `FechaEliminado` `FechaEliminado` DATETIME NULL DEFAULT NULL COMMENT '' AFTER `FechaCreacion`,
            CHANGE COLUMN `EstadoCupon` `EstadoCupon` ENUM('Creado', 'Eliminado', 'Generado', 'Redimido', 'Finalizado', 'Caducado') NOT NULL COMMENT '' ,
            ADD COLUMN `FechaFinalizado` DATETIME NULL COMMENT '' AFTER `FechaRedimido`,
            ADD COLUMN `FechaCaducado` DATETIME NULL COMMENT '' AFTER `FechaFinalizado`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
