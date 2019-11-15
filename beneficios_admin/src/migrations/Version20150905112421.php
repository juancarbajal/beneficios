<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150905112421 extends AbstractMigration
{
    public static $description = "Alter BNF_Subgrupo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Subgrupo`
        ADD COLUMN `BNF_Empresa_id` INT NOT NULL COMMENT '' AFTER `Nombre`,
        ADD INDEX `fk_BNF_Subgrupo_Empresa1_idx` (`BNF_Empresa_id` ASC)  COMMENT '';
        ALTER TABLE `BNF_Subgrupo`
        ADD CONSTRAINT `fk_BNF_Subgrupo_Empresa1`
          FOREIGN KEY (`BNF_Empresa_id`)
          REFERENCES `BNF_Empresa` (`id`)
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
