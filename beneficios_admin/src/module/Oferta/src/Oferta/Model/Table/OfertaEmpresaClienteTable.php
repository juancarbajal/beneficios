<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 26/09/15
 * Time: 10:30 PM
 */

namespace Oferta\Model\Table;


use Oferta\Model\OfertaEmpresaCliente;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class OfertaEmpresaClienteTable
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

    public function getOfertaEmpresaCliente($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Empresa Cliente $id");
        }
        return $row;
    }

    public function getOfertaEmpresaClienteExist($idOferta, $idEmpresa)
    {
        $idOferta = (int)$idOferta;
        $idEmpresa = (int)$idEmpresa;
        $select = new Select();
        $select->from('BNF_OfertaEmpresaCliente');
        $select->where('BNF_Oferta_id = ' . $idOferta . ' AND BNF_Empresa_id = ' . $idEmpresa);
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }


    public function getOfertaEmpresaClienteSeach($idOferta, $idEmpresa)
    {
        $idOferta = (int)$idOferta;
        $idEmpresa = (int)$idEmpresa;
        $rowset = $this->tableGateway->select(array('BNF_Oferta_id = ' . $idOferta, 'BNF_Empresa_id = ' . $idEmpresa));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No se puede encontrar la Relacion Oferta Empresa Cliente");
        }
        return $row;
    }

    public function getOfertaEmpresaClienteTotal($idOferta)
    {
        $idOferta = (int)$idOferta;
        $select = new Select();
        $select->from('BNF_OfertaEmpresaCliente');
        $select->where("BNF_Oferta_id = " . $idOferta . " AND Eliminado = '0'");
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaEmpresaClienteNormal($idOferta)
    {
        $idOferta = (int)$idOferta;
        $select = new Select();
        $select->from('BNF_OfertaEmpresaCliente');
        $select->join('BNF_Empresa', 'BNF_Empresa.id = BNF_OfertaEmpresaCliente.BNF_Empresa_id', array());
        $select->where
            ->equalTo("BNF_Oferta_id", $idOferta)
            ->and
            ->equalTo("BNF_OfertaEmpresaCliente.Eliminado", '0')
            ->and
            ->equalTo("ClaseEmpresaCliente", 'Normal');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertaEmpresaClienteEspecial($idOferta)
    {
        $idOferta = (int)$idOferta;
        $select = new Select();
        $select->from('BNF_OfertaEmpresaCliente');
        $select->join('BNF_Empresa', 'BNF_Empresa.id = BNF_OfertaEmpresaCliente.BNF_Empresa_id', array());
        $select->where
            ->equalTo("BNF_Oferta_id", $idOferta)
            ->and
            ->equalTo("BNF_OfertaEmpresaCliente.Eliminado", '0')
            ->and
            ->equalTo("ClaseEmpresaCliente", 'Especial');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveOfertaEmpresaCliente(OfertaEmpresaCliente $ofertaEmpresaCliente)
    {
        $data = array(
            'BNF_Oferta_id' => $ofertaEmpresaCliente->BNF_Oferta_id,
            'BNF_Empresa_id' => $ofertaEmpresaCliente->BNF_Empresa_id,
            'NumeroCupones' => $ofertaEmpresaCliente->NumeroCupones,
            'Eliminado' => $ofertaEmpresaCliente->Eliminado,
        );
        $id = (int)$ofertaEmpresaCliente->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOfertaEmpresaCliente($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Relacion Oferta Empresa Cliente no existe');
            }
        }
        return $id;
    }

    public function deleteOfertaEmpresaCliente($id)
    {
        $id = (int)$id;
        $data['Eliminado'] = '1';
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('BNF_Oferta_id' => $id));
    }

    public function getReportsNormal()
    {
        $select = new Select();
        $select->from('BNF_OfertaEmpresaCliente');
        $select->join(
            'BNF_Oferta',
            'BNF_OfertaEmpresaCliente.BNF_Oferta_id = BNF_Oferta.id',
            array('BNF_Oferta_id'=>'Titulo')
        );
        $select->join(
            'BNF_Empresa',
            'BNF_OfertaEmpresaCliente.BNF_Empresa_id = BNF_Empresa.id',
            array('BNF_Empresa_id'=>'NombreComercial')
        );

        $select->where
            ->equalTo("BNF_Empresa.ClaseEmpresaCliente", "Normal");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function getReportsEspecial()
    {
        $select = new Select();
        $select->from('BNF_OfertaEmpresaCliente');
        $select->join(
            'BNF_Oferta',
            'BNF_OfertaEmpresaCliente.BNF_Oferta_id = BNF_Oferta.id',
            array('BNF_Oferta_id'=>'Titulo')
        );
        $select->join(
            'BNF_Empresa',
            'BNF_OfertaEmpresaCliente.BNF_Empresa_id = BNF_Empresa.id',
            array('BNF_Empresa_id'=>'NombreComercial')
        );

        $select->where
            ->equalTo("BNF_Empresa.ClaseEmpresaCliente", "Especial");

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
}
