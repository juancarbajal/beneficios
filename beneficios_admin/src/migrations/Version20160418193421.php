<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160418193421 extends AbstractMigration
{
    public static $description = "Add field BNF_Empresa_id BNF_LayoutCampania table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_LayoutCampania` 
                ADD COLUMN `BNF_Empresa_id` INT NULL AFTER `BNF_Layout_id`,
                ADD INDEX `fk_BNF_LayoutCampania_1_idx` (`BNF_Empresa_id` ASC);
                ALTER TABLE `BNF_LayoutCampania` 
                ADD CONSTRAINT `fk_BNF_LayoutCampania_1`
                  FOREIGN KEY (`BNF_Empresa_id`)
                  REFERENCES `BNF_Empresa` (`id`)
                  ON DELETE NO ACTION
                  ON UPDATE NO ACTION;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
