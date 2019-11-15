<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160126022903 extends AbstractMigration
{
    public static $description = "seed BNF_DM_Dim_EstadoCivil";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO `BNF_DM_Dim_EstadoCivil`
            (`id`,`estado`) VALUES(5,'divorciado')");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
