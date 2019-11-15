<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151124152624 extends AbstractMigration
{
    public static $description = "UPDATE fields BNF_Usuario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("
        UPDATE  `BNF_Usuario` SET  `BNF_TipoUsuario_id` =  '1' WHERE  `BNF_Usuario`.`id` =3;
        UPDATE  `BNF_Usuario` SET  `BNF_TipoUsuario_id` =  '3' WHERE  `BNF_Usuario`.`id` =2;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
