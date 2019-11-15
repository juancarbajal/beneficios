<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150905122806 extends AbstractMigration
{
    public static $description = "Seed BNF_Segmento";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO `BNF_Segmento` VALUES	(1,'A'),(2,'B'),(3,'C'),(4,'Z');");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
