<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151208111327 extends AbstractMigration
{
    public static $description = "Alter field Eliminado BNF_DetalleOfertaFormulario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_DetalleOfertaFormulario`
            CHANGE COLUMN `Eliminado` `Eliminado` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '' ;

            UPDATE `BNF_DetalleOfertaFormulario`
            SET `BNF_DetalleOfertaFormulario`.Eliminado = case `BNF_DetalleOfertaFormulario`.Eliminado WHEN 2 THEN 1 ELSE 0 END;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
