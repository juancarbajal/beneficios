<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151104203806 extends AbstractMigration
{
    public static $description = "Seed table BNF_Formulario";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_Formulario` (`id`, `Descripcion`, `Eliminado`) VALUES
                      (1, 'banner', '0'),
                      (2, 'Nombres y Apellidos', '0'),
                      (3, 'Dirección', '0'),
                      (4, 'Teléfono', '0'),
                      (5, 'Email', '0'),
                      (6, 'Género', '0'),
                      (7, 'Departamento', '0'),
                      (8, 'Provincia', '0'),
                      (9, 'Ciudad', '0'),
                      (10, 'Horario de Contacto', '0'),
                      (11, 'Tipo de Contacto', '0');"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
