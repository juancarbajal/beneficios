<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151027160837 extends AbstractMigration
{
    public static $description = "ADD Slug BNF_Empresa TABLE";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE  `BNF_Empresa` ADD  `Slug` VARCHAR( 255 ) NOT NULL ;
             ALTER TABLE  `BNF_Empresa` ADD INDEX (  `Slug` ) ;
             UPDATE  `BNF_Empresa` SET  `Slug` =  `Ruc`;
             ALTER TABLE  `BNF_Empresa` ADD UNIQUE (`Slug`);"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
