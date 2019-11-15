<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160108213738 extends AbstractMigration
{
    public static $description = "Add flied BNF_Empresa_id BNF_OfertaFormCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_OfertaFormCliente`
                ADD COLUMN `BNF_Empresa_id` INT NULL AFTER `BNF_Cliente_id`,
                ADD INDEX `fk_BNF_OfertaFormCliente_3_idx` (`BNF_Empresa_id` ASC);
                ALTER TABLE `BNF_OfertaFormCliente`
                ADD CONSTRAINT `fk_BNF_OfertaFormCliente_3`
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
