<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151109170514 extends AbstractMigration
{
    public static $description = "ALTER fields [Descripcion,CondicionesUso] table BNF_Oferta";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("ALTER TABLE  `BNF_Oferta` CHANGE  `Descripcion`  `Descripcion` VARCHAR( 255 )
                      CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL COMMENT 'descripcion de la oferta';
                      ALTER TABLE  `BNF_Oferta` CHANGE  `CondicionesUso`  `CondicionesUso` LONGTEXT
                      CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
