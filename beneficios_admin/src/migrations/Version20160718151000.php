<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718151000 extends AbstractMigration
{
    public static $description = "Alter BNF2_Asignacion_Puntos_Estado_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Asignacion_Puntos_Estado_Log`
            DROP COLUMN `Operacion`,
            ADD COLUMN `Operacion` ENUM('Asignar', 'Aplicar', 'Redimir', 'Desactivar', 'Reactivar', 'Cancelar', 'Sumar', 'Restar') NULL AFTER `EstadoPuntos`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
