<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 11/11/15
 * Time: 07:22 PM
 */

namespace Application\Model\Table;

use Application\Model\OfertaFormCliente;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaFormClienteTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(null);
        return $resultSet;
    }

    public function saveOfertaFormCliente(OfertaFormCliente $cupon)
    {
        $data = $cupon->getArrayCopy();
        $data['FechaCreacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->insert($data);
    }
//    public function ssverifyLimit($idOferta, $idCliente)
//    {
//        $id = (int)$idOferta;
//        $id3 = (int)$idCliente;
//
//        $select = new Select();
//        $select->from('BNF_OfertaFormCliente');
//        $select->where
//            ->equalTo('BNF_Oferta_id', $id)
//            ->and
//            ->equalTo('BNF_Cliente_id', $id3)
//            ->and
//            ->literal('DATE(FechaCreacion) = CURDATE()');
//        $resultSet = $this->tableGateway->selectWith($select);
//        return $resultSet->count();
//    }
    public function verifyLimit($idOferta, $idOEC, $idCliente, $atributo = null)
//    public function verifyLimit($idOferta, $idCliente)
    {

        $id = (int)$idOferta;
        $id3 = (int)$idCliente;
        $id2 = (int)$idOEC;

        $select = new Select();
        $select->from('BNF_OfertaFormCliente');
        $select->where
            ->equalTo('BNF_Oferta_id', $id)
            ->and
            ->equalTo('BNF_OfertaEmpresaCliente_id', $id2)
            ->and
            ->equalTo('BNF_Cliente_id', $id3)
            ->and
            ->literal('DATE(FechaCreacion) = CURDATE()');
        if ($atributo != null) {
            $select->where->equalTo('BNF_Oferta_Atributo_id', $atributo);
        }
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }
}
