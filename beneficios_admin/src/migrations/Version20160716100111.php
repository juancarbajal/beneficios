<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716100111 extends AbstractMigration
{
    public static $description = "Create BNF2_Demanda_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF2_Demanda_Log` (
              `id` INT NOT NULL AUTO_INCREMENT,
              `BNF2_Demanda_id` INT NOT NULL,
              `BNF2_Segmento_id` INT NOT NULL,
              `BNF_Empresa_id` INT NOT NULL,
              `FechaDemanda` DATE NOT NULL,
              `PrecioMinimo` DECIMAL(10,2) NULL,
              `PrecioMaximo` DECIMAL(10,2) NULL,
              `Target` VARCHAR(255) NULL,
              `Comentarios` TEXT NULL,
              `Actualizaciones` TEXT NULL,
              `Eliminado` TINYINT(1) NOT NULL DEFAULT 0,
              `Rubros` TEXT NOT NULL,
              `EmpresaProveedor` TEXT NOT NULL,
              `EmpresasAdicionales` TEXT NOT NULL,
              `Departamentos` TEXT NOT NULL,
              `FechaCreacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`));"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
