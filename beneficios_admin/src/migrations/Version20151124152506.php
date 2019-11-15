<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151124152506 extends AbstractMigration
{
    public static $description = "ADD field BNF_Empresa_id BNF_Usuario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("
        ALTER TABLE `BNF_Usuario`
        ADD COLUMN `BNF_Empresa_id` INT NULL COMMENT '' AFTER `Correo`,
        ADD INDEX `fk_BNF_Usuario_1_idx` (`BNF_Empresa_id` ASC)  COMMENT '';
        ALTER TABLE `BNF_Usuario`
        ADD CONSTRAINT `fk_BNF_Usuario_1`
          FOREIGN KEY (`BNF_Empresa_id`)
          REFERENCES `BNF_Empresa` (`id`)
          ON DELETE NO ACTION
          ON UPDATE NO ACTION;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
