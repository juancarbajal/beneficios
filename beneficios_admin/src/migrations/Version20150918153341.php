<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150918153341 extends AbstractMigration
{
    public static $description = "Add field BNF_BolsaTotal_id BNF_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Oferta`
            ADD COLUMN `BNF_BolsaTotal_id` INT NOT NULL COMMENT '' AFTER `BNF_TipoBeneficio_id`,
            ADD INDEX `fk_BNF_Oferta_BNF_BolsaTotal_idx` (`BNF_BolsaTotal_id` ASC)  COMMENT '';
            ALTER TABLE `BNF_Oferta`
            ADD CONSTRAINT `fk_BNF_Oferta_BNF_BolsaTotal`
            FOREIGN KEY (`BNF_BolsaTotal_id`)
            REFERENCES `BNF_BolsaTotal` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION;
            ALTER TABLE `BNF_Oferta`
            CHANGE COLUMN `Premium` `Premium` INT(11) NOT NULL COMMENT '\nPremium / No Premium\n' ;
        "
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
