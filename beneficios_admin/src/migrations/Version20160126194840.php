<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160126194840 extends AbstractMigration
{
    public static $description = "Alter BNF_DM_Met_Cliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
        	"ALTER TABLE `BNF_DM_Met_Cliente`
			ADD COLUMN `EstadoCupon` ENUM('Creado', 'Eliminado', 'Generado', 'Redimido', 'Finalizado', 'Caducado') NULL AFTER `BNF_Cliente_FechaCreacion`,
			ADD COLUMN `FechaGenerado` DATETIME NULL AFTER `EstadoCupon`,
			ADD COLUMN `FechaRedimido` DATETIME NULL AFTER `FechaGenerado`,
			ADD COLUMN `BNF_Categoria_id` DATETIME NULL AFTER `FechaRedimido`,
			ADD COLUMN `Edad` INT NULL AFTER `BNF_Categoria_id`,
			ADD COLUMN `Genero` ENUM('H', 'M') NULL AFTER `Edad`,
			ADD COLUMN `TipoOferta` ENUM('Cupon', 'Lead') NULL AFTER `Genero`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
