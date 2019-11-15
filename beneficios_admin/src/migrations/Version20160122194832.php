<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160122194832 extends AbstractMigration
{
    public static $description = "Alter BNF_OfertaFormulario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE `BNF_OfertaFormulario`
            ADD COLUMN `Requerido` ENUM('0', '1') NOT NULL DEFAULT '0' AFTER `Activo`"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
