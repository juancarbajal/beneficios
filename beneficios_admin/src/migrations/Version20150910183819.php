<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150910183819 extends AbstractMigration
{
    public static $description = "Alter BNF_CampaniaUbigeo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_CampaniaUbigeo`
            DROP FOREIGN KEY `fk_BNF_CampaniaUbigeo_BNF_Ubigeo1`;
            ALTER TABLE `BNF_CampaniaUbigeo`
            CHANGE COLUMN `BNF_Ubigeo_id` `BNF_Pais_id` INT(11) NOT NULL COMMENT '' ;
            ALTER TABLE `BNF_CampaniaUbigeo`
            ADD CONSTRAINT `fk_BNF_CampaniaUbigeo_BNF_Ubigeo1`
              FOREIGN KEY (`BNF_Pais_id`)
              REFERENCES `BNF_Pais` (`id`)
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
