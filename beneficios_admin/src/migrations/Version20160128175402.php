<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20160128175402 extends AbstractMigration
{
    public static $description = "Alter mensaje_confirmacion_lead BNF_Configuraciones";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE `BNF_Configuraciones`
            SET
            `Atributo` = 'Sus datos fueron enviados satisfactoriamente, pronto lo estaremos contactando',
            `FechaActualizacion` = NOW()
            WHERE `Campo` = 'mensaje_confirmacion_lead'"
        );

    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
