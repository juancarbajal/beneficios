<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160421163801 extends AbstractMigration
{
    public static $description = "Add field Index BNF_LayoutTiendaPosicion";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_LayoutTiendaPosicion` 
                       ADD COLUMN `Index` ENUM('1', '2', '3') NOT NULL AFTER `BNF_Oferta_id`;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
