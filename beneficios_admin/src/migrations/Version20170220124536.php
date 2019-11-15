<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20170220124536 extends AbstractMigration
{
    public static $description = "CREATE BNF4_LandingReferidos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF4_LandingReferidos` (
             `id` int(11) NOT NULL AUTO_INCREMENT,
             `Nombres_Apellidos` varchar(45) DEFAULT NULL,
             `Telefonos` varchar(45) DEFAULT NULL,
             `Fecha_referencia` datetime DEFAULT NULL,
             `cliente_id` int(11) DEFAULT NULL,
             PRIMARY KEY (`id`),
             KEY `cliente_id_idx` (`cliente_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
