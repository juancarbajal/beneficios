<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718151006 extends AbstractMigration
{
    public static $description = "Alter BNF2_Campania_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF2_Campania_Log` 
              ADD COLUMN `EstadoCampania` 
              ENUM('Borrador', 'Publicado', 'Eliminado', 'Caducado') NOT NULL AFTER `Relacionado`;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
