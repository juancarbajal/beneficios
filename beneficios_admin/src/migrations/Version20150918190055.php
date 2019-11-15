<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150918190055 extends AbstractMigration
{
    public static $description = "Alter BNF_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Oferta`
            ADD COLUMN `BNF_BolsaTotal_TipoPaquete_id` INT NOT NULL COMMENT '' AFTER `Eliminado`,
            ADD COLUMN `BNF_BolsaTotal_Empresa_id` INT NOT NULL COMMENT '' AFTER `BNF_BolsaTotal_TipoPaquete_id`,
            ADD INDEX `fk_BNF_Oferta_BolsaTotal1_idx` (`BNF_BolsaTotal_TipoPaquete_id` ASC,
             `BNF_BolsaTotal_Empresa_id` ASC)  COMMENT '';
            ALTER TABLE `BNF_Oferta`
            ADD CONSTRAINT `fk_BNF_Oferta_BolsaTotal1`
              FOREIGN KEY (`BNF_BolsaTotal_TipoPaquete_id` , `BNF_BolsaTotal_Empresa_id`)
              REFERENCES `BNF_BolsaTotal` (`BNF_TipoPaquete_id` , `BNF_Empresa_id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
