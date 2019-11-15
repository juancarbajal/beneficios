<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160323102741 extends AbstractMigration
{
    public static $description = "Update BNF_Empresa table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("UPDATE `BNF_Empresa` SET CorreoPersonaAtencion = 'info@arboldelavida.com.pe' WHERE `id` = 194;
                       UPDATE `BNF_Empresa` SET CorreoPersonaAtencion = 'rogerarakaki@sushi-ito.com' WHERE `id` = 80;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
