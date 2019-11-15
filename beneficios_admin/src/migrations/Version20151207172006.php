<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151207172006 extends AbstractMigration
{
    public static $description = "Update data Proveedor, Cliente BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE BNF_Empresa AS EMP
            INNER JOIN BNF_EmpresaTipoEmpresa AS ETE ON ETE.BNF_Empresa_id = EMP.id
            SET EMP.Proveedor = b'1' WHERE ETE.BNF_TipoEmpresa_id = 1;

            UPDATE BNF_Empresa AS EMP
            INNER JOIN BNF_EmpresaTipoEmpresa AS ETE ON ETE.BNF_Empresa_id = EMP.id
            SET EMP.Cliente = b'1' WHERE ETE.BNF_TipoEmpresa_id = 2;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
