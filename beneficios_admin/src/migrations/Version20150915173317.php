<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150915173317 extends AbstractMigration
{
    public static $description = "Alter BNF_EmpresaSubgrupoCliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_EmpresaSubgrupoCliente`
            DROP FOREIGN KEY `fk_BNF_EmpresaSubgrupoCliente_BNF_Segmento1`;
            ALTER TABLE `BNF_EmpresaSubgrupoCliente`
            ADD CONSTRAINT `fk_BNF_EmpresaSubgrupoCliente_BNF_Subgrupo1`
              FOREIGN KEY (`BNF_Subgrupo_id`)
              REFERENCES `BNF_Subgrupo` (`id`)
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
