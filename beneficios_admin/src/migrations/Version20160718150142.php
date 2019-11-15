<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150142 extends AbstractMigration
{
    public static $description = "Alter BNF2_Campanias table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Campanias` 
            ADD COLUMN `PresupuestoNegociado` INT NULL AFTER `VigenciaFin`,
            ADD COLUMN `PresupuestoAsignado` INT NULL AFTER `PresupuestoNegociado`,
            ADD COLUMN `ParametroAlerta` INT NULL AFTER `PresupuestoAsignado`,
            ADD COLUMN `Comentario` TEXT NULL AFTER `ParametroAlerta`,
            ADD COLUMN `Relacionado` INT NULL AFTER `Comentario`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
