<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160126022905 extends AbstractMigration
{
    public static $description = "seed BNF_DM_Dim_Hijos";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_DM_Dim_Hijos` (`id`,`hijos`) VALUES (4,2),(5,3),(6,4),(7,5),(8,8);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
