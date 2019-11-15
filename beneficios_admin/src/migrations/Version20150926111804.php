<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150926111804 extends AbstractMigration
{
    public static $description = "Add field Eliminado BNF_OfertaCategoriaUbigeo table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_OfertaCategoriaUbigeo`
            ADD COLUMN `Eliminado` ENUM('0', '1') NULL COMMENT '' AFTER `BNF_CategoriaUbigeo_id`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
