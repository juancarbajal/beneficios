<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161114195229 extends AbstractMigration
{
    public static $description = "create BNF_Oferta_Cupon_Codigo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF_Oferta_Cupon_Codigo` (
                      `id` INT NOT NULL AUTO_INCREMENT,
                      `BNF_Oferta_id` INT NULL,
                      `Codigo` VARCHAR(40) NULL,
                      `Estado` ENUM('0', '1', '2') NULL,
                      `FechaCreacion` DATETIME NULL,
                      `FechaActualizacion` DATETIME NULL,
                      PRIMARY KEY (`id`),
                      INDEX `fk_BNF_Oferta_Cupon_Codigo_1_idx` (`BNF_Oferta_id` ASC),
                      CONSTRAINT `fk_BNF_Oferta_Cupon_Codigo_1`
                        FOREIGN KEY (`BNF_Oferta_id`)
                        REFERENCES `BNF_Oferta` (`id`)
                        ON DELETE NO ACTION
                        ON UPDATE NO ACTION);");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
