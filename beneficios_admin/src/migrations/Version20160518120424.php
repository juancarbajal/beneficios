<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160518120424 extends AbstractMigration
{
    public static $description = "Add fiel BNF_Empresa_id BNF_Cupon table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_Cupon` 
                        ADD COLUMN `BNF_Empresa_id` INT(11) NULL AFTER `BNF_OfertaEmpresaCliente_id`,
                        ADD INDEX `fk_BNF_Cupon_1_idx` (`BNF_Empresa_id` ASC);
                        ALTER TABLE `BNF_Cupon` 
                        ADD CONSTRAINT `fk_BNF_Cupon_BNF_Empresa1`
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
