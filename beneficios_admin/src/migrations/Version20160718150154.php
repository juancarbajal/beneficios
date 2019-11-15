<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718150154 extends AbstractMigration
{
    public static $description = "Seed BNF_Rubro update";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE `BNF_Rubro` SET `Eliminado`='1' WHERE `id`='7';"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
