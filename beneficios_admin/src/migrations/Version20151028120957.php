<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151028120957 extends AbstractMigration
{
    public static $description = "Create BNF_LayoutTienda table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_LayoutTienda` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
              `BNF_Layout_id` INT NOT NULL COMMENT '',
              `Index` INT NULL COMMENT '',
              `FechaCreacion` DATETIME NULL COMMENT '',
              `FechaActualizacion` DATETIME NULL COMMENT '',
              `Eliminado` ENUM('0', '1') NULL COMMENT '',
              PRIMARY KEY (`id`)  COMMENT '',
              INDEX `fk_BNF_LayoutTienda_1_idx` (`BNF_Layout_id` ASC)  COMMENT '',
              CONSTRAINT `fk_BNF_LayoutTienda_1`
                FOREIGN KEY (`BNF_Layout_id`)
                REFERENCES `BNF_Layout` (`id`)
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
