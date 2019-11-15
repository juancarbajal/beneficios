<?php
/**
 * Created by PhpStorm.
 * User: janaq-ubuntu
 * Date: 13/04/16
 * Time: 10:10 AM
 */

namespace Application\Model\Table;

use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class TarjetasOfertaTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getAllTarjetasOferta($idOferta)
    {
        $id = (int)$idOferta;
        $select = new Select();
        $select->from('BNF_Tarjetas_Oferta');
        $select->join(
            'BNF_Tarjetas',
            'BNF_Tarjetas.id = BNF_Tarjetas_Oferta.BNF_Tarjetas_id',
            array('Descripcion', 'Imagen')
        );
        $select->where->equalTo('BNF_Oferta_id', $id)
            ->AND->equalTo('BNF_Tarjetas.Eliminado', 0)
            ->AND->equalTo('BNF_Tarjetas_Oferta.Eliminado', 0);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
