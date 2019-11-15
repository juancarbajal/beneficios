<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151013232649 extends AbstractMigration
{
    public static $description = "Seed BNF_Layout";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_Layout` (`id`, `Nombre`, `imagen`, `FechaCreacion`,
                      `FechaActualizacion`, `Eliminado`)
                      VALUES (NULL, 'Una Oferta Grande', NULL, '2015-10-13 00:00:00', NULL, '0'),
                      (NULL, 'Dos Ofertas Medianas', NULL, '2015-10-13 00:00:00', NULL, '0'),
                      (NULL, 'Tres Ofertas Pequeñas', NULL, '2015-10-13 00:00:00', NULL, '0');"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
