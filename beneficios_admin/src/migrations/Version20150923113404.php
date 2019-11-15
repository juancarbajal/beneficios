<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150923113404 extends AbstractMigration
{
    public static $description = "Add field Eliminado BNF_EmpresaTipoEmpresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE  `BNF_EmpresaTipoEmpresa` ADD  `Eliminado` INT( 10 ) NOT NULL ;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
