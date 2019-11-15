<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151016224547 extends AbstractMigration
{
    public static $description = "Seed BNF_Banners";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_Banners`
                VALUES (1,'Banner 01',' ',NULL,1,NULL,NULL,'0'),
                       (2,'Banner 02',' ',NULL,2,NULL,NULL,'0'),
                       (3,'Banner 03',' ',NULL,3,NULL,NULL,'0'),
                       (4,'Banner 04',' ',NULL,4,NULL,NULL,'0');"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
