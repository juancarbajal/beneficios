<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150159 extends AbstractMigration
{
    public static $description = "Alter BNF2_Asignacion_Puntos_Estado_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Asignacion_Puntos_Estado_Log` 
            DROP COLUMN `EstadoPuntosAnterior`,
            CHANGE COLUMN `BNF_Usuario_id` `BNF_Usuario_id` INT(11) NOT NULL AFTER `Motivo`,
            ADD COLUMN `Operacion` ENUM('Suma', 'Resta', 'Aplicados', 'Redimidos') NULL AFTER `EstadoPuntos`,
            ADD COLUMN `Puntos` INT NULL AFTER `Operacion`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
