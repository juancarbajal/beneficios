<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151030204501 extends AbstractMigration
{
    public static $description = "CREATE BNF_Busqueda Table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "CREATE TABLE IF NOT EXISTS `BNF_Busqueda` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `BNF_Oferta_id` int(11) DEFAULT NULL,
            `Descripcion` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `BNF_Oferta_id` (`BNF_Oferta_id`),
            FULLTEXT KEY `Descripcion` (`Descripcion`)
            ) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
