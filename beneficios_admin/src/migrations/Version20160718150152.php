<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150152 extends AbstractMigration
{
    public static $description = "Create BNF2_Cupon_Puntos_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Cupon_Puntos_Log` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Cupon_Puntos_id` INT NOT NULL,
              `CodigoCupon` VARCHAR(45) NOT NULL,
              `EstadoCupon` ENUM('Creado', 'Eliminado', 'Generado', 'Redimido', 'Por Pagar', 'Pagado', 'Stand By', 'Anulado', 'Finalizado', 'Caducado') NOT NULL,
              `BNF2_Oferta_Puntos_id` INT NOT NULL,
              `BNF2_Oferta_Puntos_Atributos_id` INT NULL,
              `BNF_Cliente_id` INT NOT NULL,
              `BNF_Usuario_id` INT NOT NULL,
              `Comentario` TEXT NOT NULL,
              `FechaCreacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Cupon_Puntos_Log_1_idx` (`BNF2_Cupon_Puntos_id` ASC),
              INDEX `fk_BNF2_Cupon_Puntos_Log_2_idx` (`BNF2_Oferta_Puntos_id` ASC),
              INDEX `fk_BNF2_Cupon_Puntos_Log_3_idx` (`BNF2_Oferta_Puntos_Atributos_id` ASC),
              INDEX `fk_BNF2_Cupon_Puntos_Log_4_idx` (`BNF_Usuario_id` ASC),
              INDEX `fk_BNF2_Cupon_Puntos_Log_5_idx` (`BNF_Cliente_id` ASC),
              CONSTRAINT `fk_BNF2_Cupon_Puntos_Log_1`
                FOREIGN KEY (`BNF2_Cupon_Puntos_id`)
                REFERENCES `BNF2_Cupon_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Cupon_Puntos_Log_2`
                FOREIGN KEY (`BNF2_Oferta_Puntos_id`)
                REFERENCES `BNF2_Oferta_Puntos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Cupon_Puntos_Log_3`
                FOREIGN KEY (`BNF2_Oferta_Puntos_Atributos_id`)
                REFERENCES `BNF2_Oferta_Puntos_Atributos` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Cupon_Puntos_Log_4`
                FOREIGN KEY (`BNF_Usuario_id`)
                REFERENCES `BNF_Usuario` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Cupon_Puntos_Log_5`
                FOREIGN KEY (`BNF_Cliente_id`)
                REFERENCES `BNF_Cliente` (`id`)
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
