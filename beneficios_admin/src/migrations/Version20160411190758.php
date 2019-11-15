<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160411190758 extends AbstractMigration
{
    public static $description = "Alter CorreoPersonaAtencion BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_Empresa` CHANGE COLUMN `CorreoPersonaAtencion` `CorreoPersonaAtencion` VARCHAR(255) NULL;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
