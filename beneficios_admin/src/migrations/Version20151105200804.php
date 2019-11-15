<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151105200804 extends AbstractMigration
{
    public static $description = "Create BNF_BannersTienda table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_BannersTienda` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
              `BNF_Banners_id` INT NOT NULL COMMENT '',
              `Imagen` VARCHAR(100) NOT NULL COMMENT '',
              `Url` VARCHAR(255) NULL COMMENT '',
              `Posicion` INT NOT NULL COMMENT '',
              `FechaCreacion` DATETIME NULL COMMENT '',
              `FechaActualizacion` DATETIME NULL COMMENT '',
              `Eliminado` ENUM('0', '1') NOT NULL COMMENT '',
              PRIMARY KEY (`id`)  COMMENT '',
              INDEX `fk_BNF_BannersTienda_1_idx` (`BNF_Banners_id` ASC)  COMMENT '',
              CONSTRAINT `fk_BNF_BannersTienda_1`
                FOREIGN KEY (`BNF_Banners_id`)
                REFERENCES `BNF_Banners` (`id`)
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
