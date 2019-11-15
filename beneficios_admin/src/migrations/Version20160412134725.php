<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160412134725 extends AbstractMigration
{
    public static $description = "Seed BNF_TipoDocumento table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE `BNF_TipoDocumento` SET `Eliminado` = 0 WHERE `id` = 3;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
