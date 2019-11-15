<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161020124009 extends AbstractMigration
{
    public static $description = "Alter BNF_Cupon table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Cupon` 
            ADD COLUMN `BNF_Oferta_Atributo_id` INT(11) NULL AFTER `BNF_Oferta_id`,
            ADD INDEX `fk_BNF_Cupon_1_idx1` (`BNF_Oferta_Atributo_id` ASC);
            ALTER TABLE `BNF_Cupon` 
            ADD CONSTRAINT `fk_BNF_Cupon_1`
              FOREIGN KEY (`BNF_Oferta_Atributo_id`)
              REFERENCES `BNF_Oferta` (`id`)
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
