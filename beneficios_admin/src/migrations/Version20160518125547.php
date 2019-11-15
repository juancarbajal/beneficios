<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160518125547 extends AbstractMigration
{
    public static $description = "Update BNF_Cupon table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("UPDATE BNF_Cupon AS cu SET cu.BNF_Empresa_id = 
                      (SELECT ecc.BNF_Empresa_id FROM BNF_EmpresaClienteCliente AS ecc 
                      WHERE ecc.BNF_Cliente_id = cu.BNF_Cliente_id LIMIT 1 );");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
