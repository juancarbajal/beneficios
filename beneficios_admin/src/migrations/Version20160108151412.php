<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160108151412 extends AbstractMigration
{
    public static $description = "Update BNF_Configuraciones table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE `BNF_Configuraciones` SET Atributo =
               'Le informamos que se ha realizado un total de 1000 descargas de cupones de sus ofertas en nuestra web.',
              FechaActualizacion = NOW() WHERE id = 9;
              UPDATE `BNF_Configuraciones`
              SET Atributo =  '1000',
              FechaActualizacion = NOW() WHERE id = 10;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
