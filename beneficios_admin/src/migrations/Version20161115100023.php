<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100023 extends AbstractMigration
{
    public static $description = "create table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("DROP TABLE BNF2_Oferta_Puntos_Campania;");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("");
    }
}
