<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151124162043 extends AbstractMigration
{
    public static $description = "Update field";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("UPDATE `BNF_TipoUsuario` SET `Descripcion` = 'oferta' WHERE `BNF_TipoUsuario`.`id` = 5;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
