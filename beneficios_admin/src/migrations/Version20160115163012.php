<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160115163012 extends AbstractMigration
{
    public static $description = "Seed TipoDocumento Otros table BNF_TipoDocumento";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO `BNF_TipoDocumento`
          (`id`, `Nombre`, `Descripcion`, `FechaCreacion`, `FechaActualizacion`, `Eliminado`)
          VALUES (NULL, 'Otros', NULL, '2016-01-15 00:00:00', NULL, '1');
          ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
