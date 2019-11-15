<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716120123 extends AbstractMigration
{
    public static $description = "Create BNF2_OfertaEmpresaCliente_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_OfertaEmpresaCliente_Puntos` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Oferta_Puntos_id` INT NOT NULL,
              `BNF_Empresa_id` INT NOT NULL,
              `Eliminado` TINYINT(1) NULL DEFAULT 0,
              `FechaCreacion` DATETIME NULL,
              `FechaActualizacion` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_OfertaEmpresaCliente_1_idx` (`BNF2_Oferta_Puntos_id` ASC),
              INDEX `fk_BNF2_OfertaEmpresaCliente_2_idx` (`BNF_Empresa_id` ASC),
              CONSTRAINT `fk_BNF2_OfertaEmpresaCliente_1`
                FOREIGN KEY (`BNF2_Oferta_Puntos_id`)
                REFERENCES `BNF2_Oferta_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_OfertaEmpresaCliente_2`
                FOREIGN KEY (`BNF_Empresa_id`)
                REFERENCES `BNF_Empresa` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
