<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150144 extends AbstractMigration
{
    public static $description = "Create BNF2_Campania_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Campania_Log` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Campania_id` INT NOT NULL,
              `NombreCampania` VARCHAR(255) NOT NULL,
              `TipoSegmento` ENUM('Clasico', 'Personalizado') NOT NULL,
              `FechaCampania` DATE NOT NULL,
              `VigenciaInicio` DATE NOT NULL,
              `VigenciaFin` DATE NOT NULL,
              `PresupuestoNegociado` INT NOT NULL,
              `PresupuestoAsignado` INT NOT NULL,
              `ParametroAlerta` INT NOT NULL,
              `Comentario` TEXT NOT NULL,
              `Relacionado` INT NOT NULL,
              `BNF_Empresa_id` INT NOT NULL,
              `Segmentos` TEXT NOT NULL,
              `RazonEliminado` TEXT NULL,
              `FechaCreacion` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              INDEX `fk_BNF2_Campania_Log_1_idx` (`BNF2_Campania_id` ASC),
              INDEX `fk_BNF2_Campania_Log_2_idx` (`BNF_Empresa_id` ASC),
              CONSTRAINT `fk_BNF2_Campania_Log_1`
                FOREIGN KEY (`BNF2_Campania_id`)
                REFERENCES `BNF2_Campanias` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF2_Campania_Log_2`
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
