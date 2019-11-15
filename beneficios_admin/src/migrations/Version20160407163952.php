<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160407163952 extends AbstractMigration
{
    public static $description = "Seed BNF_DM_Dim_Hijos table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("UPDATE `BNF_DM_Dim_Hijos` SET `hijos` = '6' WHERE `BNF_DM_Dim_Hijos`.`id` = 8;");
        for ($i = 7; $i <= 50;$i++) {
            $this->addSql("INSERT INTO `BNF_DM_Dim_Hijos` (`hijos`) VALUES ($i);");
        }
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
