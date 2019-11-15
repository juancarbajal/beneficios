<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160718151002 extends AbstractMigration
{
    public static $description = "Alter Database charset";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "ALTER TABLE BNF2_Campanias CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
             ALTER TABLE BNF2_Asignacion_Puntos_Estado_Log CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
             ALTER TABLE BNF2_Campania_Log CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
             ALTER TABLE BNF2_Cupon_Puntos_Log CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
             ALTER TABLE BNF2_Demanda CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
             ALTER TABLE BNF2_Demanda_Log CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
             ALTER TABLE BNF2_Oferta_Puntos CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
             ALTER TABLE BNF2_Segmentos CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
             ALTER TABLE BNF2_Segmentos_Log CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
             ALTER TABLE BNF_Oferta CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
