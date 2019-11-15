<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160211105953 extends AbstractMigration
{
    public static $description = "Seed and update BNF_Formulario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("
            INSERT INTO `BNF_Formulario` VALUES (13, 'textobanner', '3', '0');
                UPDATE `BNF_Formulario` SET `Posicion` = '4' WHERE `BNF_Formulario`.`id` = 2;
                UPDATE `BNF_Formulario` SET `Posicion` = '5' WHERE `BNF_Formulario`.`id` = 3;
                UPDATE `BNF_Formulario` SET `Posicion` = '6' WHERE `BNF_Formulario`.`id` = 4;
                UPDATE `BNF_Formulario` SET `Posicion` = '7' WHERE `BNF_Formulario`.`id` = 5;
                UPDATE `BNF_Formulario` SET `Posicion` = '8' WHERE `BNF_Formulario`.`id` = 6;
                UPDATE `BNF_Formulario` SET `Posicion` = '9' WHERE `BNF_Formulario`.`id` = 7;
                UPDATE `BNF_Formulario` SET `Posicion` = '10' WHERE `BNF_Formulario`.`id` = 8;
                UPDATE `BNF_Formulario` SET `Posicion` = '11' WHERE `BNF_Formulario`.`id` = 9;
                UPDATE `BNF_Formulario` SET `Posicion` = '12' WHERE `BNF_Formulario`.`id` = 10;
                UPDATE `BNF_Formulario` SET `Posicion` = '13' WHERE `BNF_Formulario`.`id` = 11;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
