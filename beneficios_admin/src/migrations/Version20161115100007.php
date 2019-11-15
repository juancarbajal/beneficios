<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100007 extends AbstractMigration
{
    public static $description = "create BNF3_Demanda_Departamentos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Demanda_Departamentos` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF_Departamentos_id` int(11) NOT NULL,
              `BNF3_Demanda_id` int(11) NOT NULL,
              `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
              `FechaCreacion` datetime NOT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_BNF3_Demanda_Departamentos_1_idx` (`BNF3_Demanda_id`),
              KEY `fk_BNF3_Demanda_Departamentos_2_idx` (`BNF_Departamentos_id`),
              CONSTRAINT `fk_BNF3_Demanda_Departamentos_2` FOREIGN KEY (`BNF_Departamentos_id`) 
              REFERENCES `BNF_Ubigeo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Demanda_Provincias_1` FOREIGN KEY (`BNF3_Demanda_id`) 
              REFERENCES `BNF3_Demanda` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
