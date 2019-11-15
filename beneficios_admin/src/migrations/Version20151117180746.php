<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20151117180746 extends AbstractMigration
{
    public static $description = "Seed Provincias";

    public function up(MetadataInterface $schema)
    {
        $this->addSql("
            INSERT INTO `BNF_Ubigeo`(`Nombre`, `id_padre`, `BNF_Pais_id`)
            VALUES ('CHACHAPOYAS',1,1),('BAGUA',1,1),('BONGARA',1,1),('LUYA',1,1),('RODRIGUEZ DE MENDOZA',1,1),
            ('CONDORCANQUI',1,1),('UTCUBAMBA',1,1),
            ('HUARAZ',2,1),('AIJA',2,1),('BOLOGNESI',2,1),('CARHUAZ',2,1),('CASMA',2,1),('CORONGO',2,1),('HUAYLAS',2,1),
            ('HUARI',2,1),('MARISCAL LUZURIAGA',2,1),('PALLASCA',2,1),('POMABAMBA',2,1),('RECUAY',2,1),('SANTA',2,1),
            ('SIHUAS',2,1),('YUNGAY',2,1),('ANTONIO RAIMONDI',2,1),('CARLOS FERMIN FITZCARRALD',2,1),('ASUNCION',2,1),
            ('HUARMEY',2,1),('OCROS',2,1),
            ('ABANCAY',3,1),('AYMARAES',3,1),('ANDAHUAYLAS',3,1),('ANTABAMBA',3,1),('COTABAMBAS',3,1),('GRAU',3,1),
            ('CHINCHEROS',3,1),
            ('AREQUIPA',4,1),('CAYLLOMA',4,1),('CAMANA',4,1),('CARAVELI',4,1),('CASTILLA',4,1),('CONDESUYOS',4,1),
            ('ISLAY',4,1),('LA UNION',4,1),
            ('HUAMANGA',5,1),('CANGALLO',5,1),('HUANTA',5,1),('LA MAR',5,1),('LUCANAS',5,1),('PARINACOCHAS',5,1),
            ('VICTOR FAJARDO',5,1),('HUANCA SANCOS',5,1),('PAUCAR DEL SARA SARA',5,1),('SUCRE',5,1),
            ('VILCAS HUAMAN',5,1),
            ('CAJAMARCA',6,1),('CELENDIN',6,1),('CONTUMAZA',6,1),('CUTERVO',6,1),('CHOTA',6,1),('HUALGAYOC',6,1),
            ('JAEN',6,1),('SANTA CRUZ',6,1),('SAN MIGUEL',6,1),('SAN IGNACIO',6,1),('SAN MARCOS',6,1),
            ('SAN PABLO',6,1),
            ('CUSCO',7,1),('ACOMAYO',7,1),('ANTA',7,1),('CALCA',7,1),('CANAS',7,1),('CANCHIS',7,1),
            ('CHUMBIVILCAS',7,1),('ESPINAR',7,1),('LA CONVENCION',7,1),('PARURO',7,1),('PAUCARTAMBO',7,1),
            ('QUISPICANCHI',7,1),('URUBAMBA',7,1),
            ('HUANCAVELICA',8,1),('ACOBAMBA',8,1),('CASTROVIRREYNA',8,1),('TAYACAJA',8,1),('HUAYTARA',8,1),
            ('ANGARAES',8,1),('CHURCAMPA',8,1),
            ('HUANUCO',9,1),('AMBO',9,1),('DOS DE MAYO',9,1),('HUAMALIES',9,1),('MARAÑON',9,1),('LEONCIO PRADO',9,1),
            ('PACHITEA',9,1),('PUERTO INCA',9,1),('HUACAYBAMBA',9,1),('LAURICOCHA',9,1),('YAROWILCA',9,1),
            ('ICA',10,1),('CHINCHA',10,1),('NAZCA',10,1),('PISCO',10,1),('PALPA',10,1),
            ('HUANCAYO',11,1),('CONCEPCION',11,1),('JAUJA',11,1),('JUNIN',11,1),('TARMA',11,1),('YAULI',11,1),
            ('SATIPO',11,1),('CHANCHAMAYO',11,1),('CHUPACA',11,1),
            ('TRUJILLO',12,1),('BOLIVAR',12,1),('SANCHEZ CARRION',12,1),('OTUZCO',12,1),('PACASMAYO',12,1),
            ('PATAZ',12,1),('SANTIAGO DE CHUCO',12,1),('ASCOPE',12,1),('CHEPEN',12,1),('JULCAN',12,1),
            ('GRAN CHIMU',12,1),('VIRU',12,1),
            ('CHICLAYO',13,1),('FERREÑAFE',13,1),('LAMBAYEQUE',13,1),
            ('LIMA',14,1),('CAJATAMBO',14,1),('CANTA',14,1),('CAÑETE',14,1),('HUAURA',14,1),('HUAROCHIRI',14,1),
            ('YAUYOS',14,1),('HUARAL',14,1),('BARRANCA',14,1),('OYON',14,1),
            ('MAYNAS',15,1),('ALTO AMAZONAS',15,1),('LORETO',15,1),('REQUENA',15,1),('UCAYALI',15,1),
            ('MARISCAL RAMON CASTILLA',15,1),('DATEM DEL MARAÑON',15,1),
            ('TAMBOPATA',16,1),('MANU',16,1),('TAHUAMANU',16,1),
            ('MARISCAL NIETO',17,1),('GENERAL SANCHEZ CERRO',17,1),('ILO',17,1),
            ('PASCO',18,1),('DANIEL ALCIDES CARRION',18,1),('OXAPAMPA',18,1),
            ('PIURA',19,1),('AYABACA',19,1),('HUANCABAMBA',19,1),('MORROPON',19,1),('PAITA',19,1),('SULLANA',19,1),
            ('TALARA',19,1),('SECHURA',19,1),
            ('PUNO',20,1),('AZANGARO',20,1),('CARABAYA',20,1),('CHUCUITO',20,1),('HUANCANE',20,1),('LAMPA',20,1),
            ('MELGAR',20,1),('SANDIA',20,1),('SAN ROMAN',20,1),('YUNGUYO',20,1),('SAN ANTONIO DE PUTINA',20,1),
            ('EL COLLAO',20,1),('MOHO',20,1),
            ('MOYOBAMBA',21,1),('HUALLAGA',21,1),('LAMAS',21,1),('MARISCAL CACERES',21,1),('RIOJA',21,1),
            ('SAN MARTIN',21,1),('BELLAVISTA',21,1),('TOCACHE',21,1),('PICOTA',21,1),('EL DORADO',21,1),
            ('TACNA',22,1),('TARATA',22,1),('JORGE BASADRE',22,1),('CANDARAVE',22,1),
            ('TUMBES',23,1),('CONTRALMIRANTE VILLAR',23,1),('ZARUMILLA',23,1),
            ('CORONEL PORTILLO',25,1),('PADRE ABAD',25,1),('ATALAYA',25,1),('PURUS',25,1);
        ");
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
