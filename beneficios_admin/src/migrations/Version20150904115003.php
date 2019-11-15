<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150904115003 extends AbstractMigration
{
    public static $description = "Delete field Eliminado BNF_Cliente table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_Cliente` DROP COLUMN `Eliminado`;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
