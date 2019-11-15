<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160716120130 extends AbstractMigration
{
    public static $description = "DROP BNF2_OfertaEmpresaCliente_Puntos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "DROP TABLE `BNF2_OfertaEmpresaCliente_Puntos`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
