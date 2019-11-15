<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151104202621 extends AbstractMigration
{
    public static $description = "Create BNF_DetalleOfertaFormulario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_DetalleOfertaFormulario` (
              `BNF_OfertaFormulario_id` INT NOT NULL COMMENT '',
              `Descripcion` VARCHAR(255) NULL COMMENT '',
              `Eliminado` ENUM('0', '1') NOT NULL DEFAULT '0' COMMENT '',
              INDEX `fk_BNF_DetalleOfertaFormulario_BNF_OfertaFormulario1_idx`
              (`BNF_OfertaFormulario_id` ASC)  COMMENT '',
              CONSTRAINT `fk_BNF_DetalleOfertaFormulario_BNF_OfertaFormulario1`
                FOREIGN KEY (`BNF_OfertaFormulario_id`)
                REFERENCES `BNF_OfertaFormulario` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
