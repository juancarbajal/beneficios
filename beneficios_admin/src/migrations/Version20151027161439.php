<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151027161439 extends AbstractMigration
{
    public static $description = "ADD Slug BNF_Oferta TABLE";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE  `BNF_Oferta` ADD  `Slug` VARCHAR( 255 ) NOT NULL ;
             ALTER TABLE  `BNF_Oferta` ADD INDEX (  `Slug` ) ;
             UPDATE  `BNF_Oferta` SET  `Slug` =  `id`;
             ALTER TABLE  `BNF_Oferta` ADD UNIQUE ( `Slug` );"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
