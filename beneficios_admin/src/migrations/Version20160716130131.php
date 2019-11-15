<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716130131 extends AbstractMigration
{
    public static $description = "Alter BNF2_Cupon_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Cupon_Puntos` 
            ADD COLUMN `PuntosUsuario` INT NULL AFTER `EstadoCupon`,
            ADD COLUMN `PuntosUtilizados` INT NULL AFTER `PuntosUsuario`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
