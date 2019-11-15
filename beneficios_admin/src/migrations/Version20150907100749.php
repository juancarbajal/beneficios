<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150907100749 extends AbstractMigration
{
    public static $description = "Update field Contrasenia BNF_Usuario table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            'ALTER TABLE  `BNF_Usuario` CHANGE  `Contrasenia`  `Contrasenia`
                      VARCHAR( 100 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ;'
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
