<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160717140132 extends AbstractMigration
{
    public static $description = "Seed BNF_Categoria puntos";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_Categoria`(`Nombre`, `Slug`, `FechaCreacion`,`Eliminado`) 
              VALUES ('Puntos','puntos','2016-08-01 00:00:00',1);
              
              INSERT INTO `BNF_CategoriaUbigeo`(`BNF_Categoria_id`, `BNF_Pais_id`, `Eliminado`) 
              VALUES (9,1,1);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
