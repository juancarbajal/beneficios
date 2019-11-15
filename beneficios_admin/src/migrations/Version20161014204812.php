<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161014204812 extends AbstractMigration
{
    public static $description = "Seed BNF2_Asignacion_Puntos_Estado_Log";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 1;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 2;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 3;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 4;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 5;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 6;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 7;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 8;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 9;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 10;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 11;
            UPDATE BNF2_Asignacion_Puntos_Estado_Log SET FechaCreacion = DATE_SUB(FechaCreacion, INTERVAL 5 HOUR) WHERE id = 12;"
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
