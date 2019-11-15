<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161020124011 extends AbstractMigration
{
    public static $description = "Seed BNF_Busqueda Insert";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO BNF_Busqueda (`id`, `BNF_Oferta_id`,`TipoOferta`,`Descripcion`,`Empresa`) 
              (SELECT NULL , `id`, 0, `NombreComercial`, 1 FROM BNF_Empresa);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
