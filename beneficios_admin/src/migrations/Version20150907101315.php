<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150907101315 extends AbstractMigration
{
    public static $description = "Seed BNF_Pais";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO BNF_Pais	VALUES 	(1,'Peru');");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
