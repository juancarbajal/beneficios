<?php
/**
 * Created by PhpStorm.
 * User: janaqlap1
 * Date: 28/10/15
 * Time: 18:50
 */

namespace Application\Model\Table;

use Application\Model\Cupon;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CuponTable
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

    public function getCupon($idOferta)
    {
        $id = (int)$idOferta;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id' => $id, 'EstadoCupon' => 'Creado'));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function saveCupon(Cupon $cupon)
    {
        $data = $cupon->getArrayCopy();
        $data['FechaCreacion'] = date("Y-m-d H:i:s");
        $data['FechaGenerado'] = date("Y-m-d H:i:s");
        $this->tableGateway->insert($data);
        return $this->tableGateway->getLastInsertValue();
    }

    public function updateCupon($idEmpresaCliente, $idEmpresa, $idCliente, $idCupon, $idCategoria, $id_correoCliente)
    {
        $data = array();
        $data['BNF_OfertaEmpresaCliente_id'] = $idEmpresaCliente;
        $data['BNF_Empresa_id'] = $idEmpresa;
        if ($idCliente != '') {
            $data['BNF_Cliente_id'] = $idCliente;
        }
        $data['EstadoCupon'] = 'Generado';
        $data['FechaGenerado'] = date("Y-m-d H:i:s");
        $data['BNF_Categoria_id'] = $idCategoria;
        if ($id_correoCliente != null) {
            $data['BNF_ClienteCorreo_id'] = $id_correoCliente;
        }
        return $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function setCuponCode($codigo, $idCupon)
    {
        $data = array();
        $data['CodigoCupon'] = $codigo;
        return $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function verifyLimit($idOferta, $idOEC, $idCliente, $atributo = null)
    {
        $id = (int)$idOferta;
        $id2 = (int)$idOEC;
        $id3 = (int)$idCliente;

        $select = new Select();
        $select->from('BNF_Cupon');
        $select->where
            ->equalTo('BNF_Oferta_id', $id)
            ->and
            ->equalTo('BNF_OfertaEmpresaCliente_id', $id2)
            ->and
            ->equalTo('BNF_Cliente_id', $id3)
            ->and
            ->literal('DATE(FechaGenerado) = CURDATE()');

        if ($atributo != null) {
            $select->where->equalTo('BNF_Oferta_Atributo_id', $atributo);
        }

        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getTotalRedimidosEmpresa($empresa)
    {
        $select = new Select();
        $select->from('BNF_Cupon');
        $select->join(
            'BNF_Oferta',
            'BNF_Oferta.id = BNF_Cupon.BNF_Oferta_id'
        );
        $select->where->equalTo('BNF_BolsaTotal_Empresa_id', $empresa)
            ->and->equalTo('EstadoCupon', 'Generado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getCuponValid($idOferta, $atributo)
    {
        $select = new Select();
        $select->from('BNF_Cupon');
        $select->where->equalTo('BNF_Oferta_id', $idOferta);
        if ($atributo != null) {
            $select->where->equalTo('BNF_Oferta_Atributo_id', $atributo);
        }
        $select->where->equalTo('EstadoCupon', 'Creado');
        $select->limit(1);
        //echo str_replace('"','',$select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }
}
