<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151124152246 extends AbstractMigration
{
    public static $description = "Seed BNF_TipoUsuario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("
        INSERT INTO `BNF_TipoUsuario` VALUES (NULL, 'Oferta', 'ofeta', '2015-11-24 15:13:23', NULL, '0'),
        (NULL, 'Proveedor', 'proveedor', '2015-11-24 15:13:23', NULL, '0');
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
