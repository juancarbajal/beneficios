<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100015 extends AbstractMigration
{
    public static $description = "create BNF3_Oferta_Premios_Atributos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Oferta_Premios_Atributos` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF3_Oferta_Premios_id` int(11) NOT NULL,
              `NombreAtributo` text NOT NULL,
              `PrecioVentaPublico` int(11) NOT NULL,
              `PrecioBeneficio` int(11) NOT NULL,
              `Stock` int(11) NOT NULL,
              `FechaVigencia` date NOT NULL,
              `Eliminado` tinyint(1) NOT NULL DEFAULT '0',
              `FechaCreacion` datetime NOT NULL,
              `FechaActualizacion` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `fk_BNF3_Oferta_Premios_Atributos_1_idx` (`BNF3_Oferta_Premios_id`),
              CONSTRAINT `fk_BNF3_Oferta_Premios_Atributos_1` FOREIGN KEY (`BNF3_Oferta_Premios_id`) 
              REFERENCES `BNF3_Oferta_Premios` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("DROP TABLE BNF3_Oferta_Premios_Atributos");
    }
}
