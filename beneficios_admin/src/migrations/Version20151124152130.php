<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151124152130 extends AbstractMigration
{
    public static $description = "UPDATE fields (*) BNF_TipoUsuario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("
        UPDATE  `BNF_TipoUsuario` SET  `Nombre` =  'Super Administrador' WHERE  `BNF_TipoUsuario`.`id` =1;
        UPDATE  `BNF_TipoUsuario` SET  `Descripcion` =  'super' WHERE  `BNF_TipoUsuario`.`id` =1;
        UPDATE  `BNF_TipoUsuario` SET  `Descripcion` =  'admin' WHERE  `BNF_TipoUsuario`.`id` =2;
        UPDATE  `BNF_TipoUsuario` SET  `Nombre` =  'Asesor' WHERE  `BNF_TipoUsuario`.`id` =3;
        UPDATE  `BNF_TipoUsuario` SET  `Descripcion` =  'asesor' WHERE  `BNF_TipoUsuario`.`id` =3;
        UPDATE  `BNF_TipoUsuario` SET  `Descripcion` =  'demanda' WHERE  `BNF_TipoUsuario`.`id` =4;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
