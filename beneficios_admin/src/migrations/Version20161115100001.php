<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20161115100001 extends AbstractMigration
{
    public static $description = "create BNF3_Campania_Log table";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("CREATE TABLE `BNF3_Campania_Log` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `BNF3_Campania_id` int(11) NOT NULL,
              `NombreCampania` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
              `TipoSegmento` enum('Clasico','Personalizado') COLLATE utf8_unicode_ci NOT NULL,
              `FechaCampania` date NOT NULL,
              `VigenciaInicio` date NOT NULL,
              `VigenciaFin` date NOT NULL,
              `PresupuestoNegociado` int(11) NOT NULL,
              `PresupuestoAsignado` int(11) NOT NULL,
              `ParametroAlerta` int(11) NOT NULL,
              `Comentario` mediumtext COLLATE utf8_unicode_ci NOT NULL,
              `Relacionado` int(11) NOT NULL,
              `EstadoCampania` enum('Borrador','Publicado','Eliminado','Caducado') COLLATE utf8_unicode_ci NOT NULL,
              `BNF_Empresa_id` int(11) NOT NULL,
              `Segmentos` mediumtext COLLATE utf8_unicode_ci NOT NULL,
              `RazonEliminado` mediumtext COLLATE utf8_unicode_ci,
              `FechaCreacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `fk_BNF3_Campania_Log_1_idx` (`BNF3_Campania_id`),
              KEY `fk_BNF3_Campania_Log_2_idx` (`BNF_Empresa_id`),
              CONSTRAINT `fk_BNF3_Campania_Log_1` FOREIGN KEY (`BNF3_Campania_id`) 
              REFERENCES `BNF3_Campanias` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
              CONSTRAINT `fk_BNF3_Campania_Log_2` FOREIGN KEY (`BNF_Empresa_id`) 
              REFERENCES `BNF_Empresa` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        $this->addSql("DROP TABLE BNF3_Campania_Log");
    }
}
