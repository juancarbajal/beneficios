<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150151 extends AbstractMigration
{
    public static $description = "Alter BNF2_Cupon_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Cupon_Puntos` 
            ADD COLUMN `FechaPorPagar` DATETIME NULL AFTER `FechaRedimido`,
            ADD COLUMN `FechaPagado` DATETIME NULL AFTER `FechaPorPagar`,
            ADD COLUMN `FechaStandBy` DATETIME NULL AFTER `FechaPagado`,
            ADD COLUMN `FechaAnulado` DATETIME NULL AFTER `FechaStandBy`,
            CHANGE COLUMN `FechaCaducado` `FechaCaducado` DATETIME NULL DEFAULT NULL AFTER `FechaAnulado`,
            CHANGE COLUMN `EstadoCupon` `EstadoCupon` ENUM('Creado', 'Eliminado', 'Generado', 'Redimido', 'Por Pagar', 'Pagado', 'Stand By', 'Anulado', 'Finalizado', 'Caducado') NOT NULL ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
