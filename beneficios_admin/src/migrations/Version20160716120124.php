<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716120124 extends AbstractMigration
{
    public static $description = "Create BNF2_Cupon_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Cupon_Puntos` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_OfertaEmpresaCliente_id` INT NULL,
              `BNF_Empresa_id` INT NULL,
              `BNF_Cliente_id` INT NULL,
              `CodigoCupon` VARCHAR(45) NULL,
              `EstadoCupon` ENUM('Creado', 'Eliminado', 'Generado', 'Redimido', 'Finalizado', 'Caducado') NOT NULL,
              `BNF2_Oferta_Puntos_id` INT NOT NULL,
              `BNF_Categoria_id` VARCHAR(255) NULL,
              `BNF_ClienteCorreo_id` INT NULL,
              `FechaCreacion` DATETIME NULL,
              `FechaEliminado` DATETIME NULL,
              `FechaGenerado` DATETIME NULL,
              `FechaRedimido` DATETIME NULL,
              `FechaFinalizado` DATETIME NULL,
              `FechaCaducado` DATETIME NULL,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Cupon_Puntos_1_idx` (`BNF2_OfertaEmpresaCliente_id` ASC),
              INDEX `fk_BNF2_Cupon_Puntos_2_idx` (`BNF_Empresa_id` ASC),
              INDEX `fk_BNF2_Cupon_Puntos_3_idx` (`BNF_Cliente_id` ASC),
              INDEX `fk_BNF2_Cupon_Puntos_4_idx` (`BNF2_Oferta_Puntos_id` ASC),
              INDEX `fk_BNF2_Cupon_Puntos_5_idx` (`BNF_ClienteCorreo_id` ASC),
              CONSTRAINT `fk_BNF2_Cupon_Puntos_1`
                FOREIGN KEY (`BNF2_OfertaEmpresaCliente_id`)
                REFERENCES `BNF2_OfertaEmpresaCliente_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Cupon_Puntos_2`
                FOREIGN KEY (`BNF_Empresa_id`)
                REFERENCES `BNF_Empresa` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Cupon_Puntos_3`
                FOREIGN KEY (`BNF_Cliente_id`)
                REFERENCES `BNF_Cliente` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Cupon_Puntos_4`
                FOREIGN KEY (`BNF2_Oferta_Puntos_id`)
                REFERENCES `BNF2_Oferta_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Cupon_Puntos_5`
                FOREIGN KEY (`BNF_ClienteCorreo_id`)
                REFERENCES `BNF_ClienteCorreo` (`id`)
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
