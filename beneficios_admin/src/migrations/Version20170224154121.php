<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170224154121 extends AbstractMigration
{
    public static $description = "ALTER BNF2_Asignacion_Puntos_Estado_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Asignacion_Puntos_Estado_Log` 
                ADD COLUMN `FechaActualizacion` TIMESTAMP NULL AFTER `FechaCreacion`,
                ADD COLUMN `Estado_Cron` TINYINT(1) NOT NULL DEFAULT 0 AFTER `FechaActualizacion`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
