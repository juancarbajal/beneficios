<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150903104739 extends AbstractMigration
{
    public static $description = "Create BNF_LayoutCampania table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_LayoutCampania` (
          `id` INT NOT NULL AUTO_INCREMENT COMMENT '',
          `BNF_Campanias_id` INT NOT NULL COMMENT '',
          `BNF_Layout_id` INT NOT NULL COMMENT '',
          PRIMARY KEY (`id`)  COMMENT '',
          INDEX `fk_BNF_LayoutCampania_BNF_Campanias1_idx` (`BNF_Campanias_id` ASC)  COMMENT '',
          INDEX `fk_BNF_LayoutCampania_BNF_Layout1_idx` (`BNF_Layout_id` ASC)  COMMENT '',
          CONSTRAINT `fk_BNF_LayoutCampania_BNF_Campanias1`
            FOREIGN KEY (`BNF_Campanias_id`)
            REFERENCES `BNF_Campanias` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION,
          CONSTRAINT `fk_BNF_LayoutCampania_BNF_Layout1`
            FOREIGN KEY (`BNF_Layout_id`)
            REFERENCES `BNF_Layout` (`id`)
            ON DELETE NO ACTION
            ON UPDATE NO ACTION)
        ENGINE = InnoDB;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
