<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100026 extends AbstractMigration
{
    public static $description = "Seed categoroa premios";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("INSERT INTO `BNF_Categoria` 
        VALUES ('10', 'Premios', 'premios', 'categoria premios', CURRENT_DATE(), CURRENT_DATE(), '1');
        INSERT INTO `BNF_CategoriaUbigeo` VALUES (NULL, '10', '1', '1');");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
