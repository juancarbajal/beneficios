<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100002 extends AbstractMigration
{
    public static $description = "create BNF3_Campanias_Empresas table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Campanias_Empresas` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF3_Campania_id` int(11) NOT NULL,
              `BNF_Empresa_id` int(11) NOT NULL,
              `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
              `FechaCreacion` datetime DEFAULT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_BNF3_Campanias_Empresas_1_idx` (`BNF3_Campania_id`),
              KEY `fk_BNF3_Campanias_Empresas_2_idx` (`BNF_Empresa_id`),
              CONSTRAINT `fk_BNF3_Campanias_Empresas_1` FOREIGN KEY (`BNF3_Campania_id`) 
              REFERENCES `BNF3_Campanias` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Campanias_Empresas_2` FOREIGN KEY (`BNF_Empresa_id`) 
              REFERENCES `BNF_Empresa` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
