<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160126022900 extends AbstractMigration
{
    public static $description = "alter field estado BNF_DM_Dim_EstadoCivil table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_DM_Dim_EstadoCivil`
            CHANGE COLUMN `estado` `estado` ENUM('soltero', 'casado', 'viudo', 'no-definido', 'divorciado') NULL DEFAULT 'no-definido' ;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
