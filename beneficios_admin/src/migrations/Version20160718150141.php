<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150141 extends AbstractMigration
{
    public static $description = "Seed Banner para oferta puntos";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO `BNF_Banners` 
                      (`id`, `Nombre`, `Descripcion`, `FechaCreacion`, `FechaActualizacion`, `Eliminado`) 
                      VALUES (NULL, 'Banner Oferta', NULL, CURRENT_DATE(), CURRENT_DATE(), '0');");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
