<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151106173222 extends AbstractMigration
{
    public static $description = "Seed update BNF_Formulario";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE `BNF_Formulario` SET `Posicion` = 2 WHERE `id` = 1;
              UPDATE `BNF_Formulario` SET `Posicion` = 3 WHERE `id` = 2;
              UPDATE `BNF_Formulario` SET `Posicion` = 4 WHERE `id` = 3;
              UPDATE `BNF_Formulario` SET `Posicion` = 5 WHERE `id` = 4;
              UPDATE `BNF_Formulario` SET `Posicion` = 6 WHERE `id` = 5;
              UPDATE `BNF_Formulario` SET `Posicion` = 7 WHERE `id` = 6;
              UPDATE `BNF_Formulario` SET `Posicion` = 8 WHERE `id` = 7;
              UPDATE `BNF_Formulario` SET `Posicion` = 9 WHERE `id` = 8;
              UPDATE `BNF_Formulario` SET `Posicion` = 10 WHERE `id` = 9;
              UPDATE `BNF_Formulario` SET `Posicion` = 11 WHERE `id` = 10;
              UPDATE `BNF_Formulario` SET `Posicion` = 12 WHERE `id` = 11;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
