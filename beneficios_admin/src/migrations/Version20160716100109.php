<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716100109 extends AbstractMigration
{
    public static $description = "Alter BNF2_Demanda table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Demanda` 
                CHANGE COLUMN `ConceptoCampania` `BNF2_Segmento_id` INT NOT NULL ,
                ADD COLUMN `Actualizaciones` TEXT NULL AFTER `Comentarios`,
                ADD INDEX `fk_BNF2_Demanda_2_idx` (`BNF2_Segmento_id` ASC);
                ALTER TABLE `BNF2_Demanda` 
                ADD CONSTRAINT `fk_BNF2_Demanda_2`
                  FOREIGN KEY (`BNF2_Segmento_id`)
                  REFERENCES `BNF2_Segmentos` (`id`)
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
