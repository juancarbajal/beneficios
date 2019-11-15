<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150910183815 extends AbstractMigration
{
    public static $description = "Alter BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Empresa`
            DROP FOREIGN KEY `fk_BNF_Empresa_BNF_Ubigeo1`,
            DROP FOREIGN KEY `fk_BNF_Empresa_BNF_Ubigeo2`;
            ALTER TABLE `BNF_Empresa`
            CHANGE COLUMN `BNF_Ubigeo_id_envio` `BNF_Ubigeo_id_envio` INT(11) NULL COMMENT '' ,
            CHANGE COLUMN `BNF_Ubigeo_id_legal` `BNF_Ubigeo_id_legal` INT(11) NULL COMMENT '' ;
            ALTER TABLE `BNF_Empresa`
            ADD CONSTRAINT `fk_BNF_Empresa_BNF_Ubigeo1`
              FOREIGN KEY (`BNF_Ubigeo_id_envio`)
              REFERENCES `BNF_Ubigeo` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION,
            ADD CONSTRAINT `fk_BNF_Empresa_BNF_Ubigeo2`
              FOREIGN KEY (`BNF_Ubigeo_id_legal`)
              REFERENCES `BNF_Ubigeo` (`id`)
              ON DELETE NO ACTION
              ON UPDATE NO ACTION;
            "
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
