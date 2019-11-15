<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170220124534 extends AbstractMigration
{
    public static $description = "CREATE BNF4_LandingClientesColaboradores table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF4_LandingClientesColaboradores` (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `Nombres_Apellidos` varchar(120) DEFAULT NULL,
             `Telefonos` varchar(10) DEFAULT NULL,
             `Email` varchar(60) DEFAULT NULL,
             `Especialista` varchar(80) DEFAULT NULL,
             `Creado` datetime DEFAULT NULL,
             `Documento` varchar(15) DEFAULT NULL,
             `Tipo` varchar(45) DEFAULT NULL,
             PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
