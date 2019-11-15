<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161114195228 extends AbstractMigration
{
    public static $description = "add field TipoEspecial BNF_Oferta table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE `BNF_Oferta` 
                        ADD COLUMN `TipoEspecial` ENUM('0', '1') NULL DEFAULT '0' AFTER `TipoAtributo`;
                        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
