<?php

namespace ZfSimpleMigrations\Migrations;

use ZfSimpleMigrations\Library\AbstractMigration;
use Zend\Db\Metadata\MetadataInterface;

class Version20150910184503 extends AbstractMigration
{
    public static $description = "Seed BNF_Usuario";

    public function up(MetadataInterface $schema)
    {
        $this->addSql(
            "INSERT INTO `BNF_Usuario`
            VALUES (null,2,1,'Solman','Vaisman',NULL,'d41d8cd98f00b204e9800998ecf8427e','44602503','solman28@gmail.com',
            '2015-09-04 03:08:53','2015-09-09 18:00:10',NULL,0),
                  (null,1,1,'Juan','Perez',NULL,NULL,'45787898','juan@yopmail.com','2015-09-06 15:21:49',NULL,NULL,0),
                  (null,2,1,'admin','admin','admin','96e79218965eb72c92a549dd5a330112','12345678','admin@mail.com',
                  '2015-09-08 00:00:00',NULL,'2015-09-10 18:09:43',NULL);
        "
        );
    }

    public function down(MetadataInterface $schema)
    {
        //throw new \RuntimeException('No way to go down!');
        //$this->addSql(/*Sql instruction*/);
    }
}
