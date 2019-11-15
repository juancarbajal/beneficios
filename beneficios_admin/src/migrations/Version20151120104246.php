<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151120104246 extends AbstractMigration
{
    public static $description = "Seed dias_expiracion";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO `BNF_Configuraciones` VALUES (NULL,'dias_expiracion','1',NULL,NULL);");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
