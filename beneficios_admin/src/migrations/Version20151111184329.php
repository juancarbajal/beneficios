<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151111184329 extends AbstractMigration
{
    public static $description = "Create BNF_OfertaFormCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE `BNF_OfertaFormCliente` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
              `BNF_Oferta_id` INT NOT NULL COMMENT '',
              `BNF_Cliente_id` INT NOT NULL COMMENT '',
              `FechaCreacion` DATETIME NULL COMMENT '',
              `FechaActualizacion` DATETIME NULL COMMENT '',
              PRIMARY KEY (`id`)  COMMENT '',
              INDEX `fk_BNF_OfertaFormCliente_1_idx` (`BNF_Oferta_id` ASC)  COMMENT '',
              INDEX `fk_BNF_OfertaFormCliente_2_idx` (`BNF_Cliente_id` ASC)  COMMENT '',
              CONSTRAINT `fk_BNF_OfertaFormCliente_1`
                FOREIGN KEY (`BNF_Oferta_id`)
                REFERENCES `BNF_Oferta` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF_OfertaFormCliente_2`
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
