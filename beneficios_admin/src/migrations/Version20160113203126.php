<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160113203126 extends AbstractMigration
{
    public static $description = "ADD field BNF_Categoria_id tables BNF_Cupon, BNF_OfertaFormCliente";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("
        ALTER TABLE `BNF_Cupon` ADD `BNF_Categoria_id` VARCHAR(255) NOT NULL AFTER `BNF_Oferta_id`;
        ALTER TABLE `BNF_OfertaFormCliente` ADD `BNF_Categoria_id` VARCHAR(255) NOT NULL AFTER `BNF_Empresa_id`;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
