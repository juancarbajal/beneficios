<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100024 extends AbstractMigration
{
    public static $description = "create BNF_LayoutPremios table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF_LayoutPremios` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF_Layout_id` int(11) NOT NULL,
              `BNF_Empresa_id` int(11) DEFAULT NULL,
              `Index` int(11) DEFAULT NULL,
              `FechaCreacion` datetime DEFAULT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
              `Eliminado` tinyint(1) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_BNF_LayoutPremios_1_idx` (`BNF_Layout_id`),
              KEY `fk_BNF_LayoutPremios_2_idx` (`BNF_Empresa_id`),
              CONSTRAINT `fk_BNF_LayoutPremios_1` FOREIGN KEY (`BNF_Layout_id`) 
              REFERENCES `BNF_Layout` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_LayoutPremios_2` FOREIGN KEY (`BNF_Empresa_id`) 
              REFERENCES `BNF_Empresa` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
            SELECT * FROM beneficios_pro.migration_version;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
