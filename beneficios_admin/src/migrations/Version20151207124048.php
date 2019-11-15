<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151207124048 extends AbstractMigration
{
    public static $description = "Add Indices 01";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Cupon`
            ADD INDEX `EstadoCupon_index` (`EstadoCupon` ASC)  COMMENT '';

            ALTER TABLE `BNF_Oferta`
            ADD INDEX `Eliminado_index` (`Eliminado` ASC)  COMMENT '';"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
