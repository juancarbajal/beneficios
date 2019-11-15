<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 06/10/15
 * Time: 04:45 PM
 */

namespace Cupon\Model\Table;

use Cupon\Model\Cupon;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CuponTable
{
    protected $tableGateway;
    protected $oferta;
    protected $ultima;
    protected $atributo;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getCupon($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function searchCupon($codigo, $empresa_id)
    {

        $select = new Select();
        $select->from('BNF_Cupon');
        $select->join('BNF_Oferta', 'BNF_Oferta.id = BNF_Cupon.BNF_Oferta_id', array());
        if ($empresa_id != null) {
            $select->where
                ->equalTo('BNF_Oferta.BNF_BolsaTotal_Empresa_id', $empresa_id);
        }
        $select->where
            ->equalTo('BNF_Cupon.CodigoCupon', $codigo)
            ->and
            ->notEqualTo('EstadoCupon', 'Eliminado')
            ->and
            ->notEqualTo('EstadoCupon', 'Creado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCuponesOferta($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select("BNF_Oferta_id = " . $id . " AND EstadoCupon != 'Eliminado'");
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No hay cupones de la Oferta $id");
        }
        return $row;
    }

    public function getTotalCupones($id)
    {
        $resultSet = $this->tableGateway->select("BNF_Oferta_id = " . $id);
        return $resultSet->count();
    }

    public function getTotalNoEliminado($id)
    {
        $resultSet = $this->tableGateway->select("BNF_Oferta_id = " . $id . " AND EstadoCupon != 'Eliminado'");
        return $resultSet->count();
    }

    public function saveCupon(Cupon $cupon)
    {
        $data = array(
            'BNF_OfertaEmpresaCliente_id' => $cupon->BNF_OfertaEmpresaCliente_id,
            'BNF_Cliente_id' => $cupon->BNF_Cliente_id,
            'EstadoCupon' => $cupon->EstadoCupon,
            'BNF_Oferta_id' => $cupon->BNF_Oferta_id,
            'BNF_Oferta_Atributo_id' => $cupon->BNF_Oferta_Atributo_id,
            'FechaEliminado' => $cupon->FechaEliminado,
            'FechaRedimido' => $cupon->FechaRedimido,
            'FechaGenerado' => $cupon->FechaGenerado,
            'FechaFinalizado' => $cupon->FechaFinalizado,
            'FechaCaducado' => $cupon->FechaCaducado,
        );
        $id = (int)$cupon->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCupon($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('El Cupon no existe.');
            }
        }
        return $id;
    }

    public function getLastCupon($oferta, $ultimo = null, $atributoID = null)
    {
        $this->oferta = (int)$oferta;
        $this->ultima = (int)$ultimo;
        $this->atributo = (int)$atributoID;
        $rowset = $this->tableGateway->select(
            function (Select $select) {
                $select->where->equalTo('BNF_Oferta_id', $this->oferta);
                $select->where->notLike('EstadoCupon', 'Eliminado');
                $select->where->notLike('EstadoCupon', 'Generado');
                $select->where->notLike('EstadoCupon', 'Redimido');
                if ($this->ultima != 0) {
                    $select->where->lessThan('id', $this->ultima);
                }
                if ($this->atributo != 0) {
                    $select->where->equalTo('BNF_Oferta_Atributo_id', $this->atributo);
                }
                $select->order("id DESC")->limit(1);
            }
        );

        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getExpiredCuponGenerate($oferta, $atributo = null)
    {
        $select = new Select();
        $select->from('BNF_Cupon');
        $select->where->equalTo('BNF_Oferta_id', $oferta)
            ->and->equalTo('EstadoCupon', 'Generado');

        if (!empty($atributo)) {
            $select->where->equalTo('BNF_Oferta_Atributo_id', $atributo);
        }
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getExpiredCuponCreate($oferta, $atributo = null)
    {
        $select = new Select();
        $select->from('BNF_Cupon');
        $select->where->equalTo('BNF_Oferta_id', $oferta)
            ->and->equalTo('EstadoCupon', 'Creado');

        if (!empty($atributo)) {
            $select->where->equalTo('BNF_Oferta_Atributo_id', $atributo);
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getExpiredCuponFinalized($oferta)
    {
        $this->oferta = (int)$oferta;
        $rowset = $this->tableGateway->select(
            function (Select $select) {
                $select->where->equalTo('BNF_Oferta_id', $this->oferta);
                $select->where->equalTo('EstadoCupon', 'Finalizado');
            }
        );
        if (!$rowset) {
            return false;
        }
        return $rowset;
    }

    public function deleteCupon($idOferta, $idCupon)
    {
        $data['FechaEliminado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Eliminado';
        $this->tableGateway->update($data, array('id' => $idCupon, 'BNF_Oferta_id' => $idOferta));
    }

    public function redimirCupon($idCupon)
    {
        $data['FechaRedimido'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Redimido';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function updateXofertaFinalizado($idOferta, $idAtributo = null)
    {
        $data['FechaFinalizado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Finalizado';

        $where['BNF_Oferta_id'] = $idOferta;
        $where['EstadoCupon'] = 'Creado';
        if (!empty($idAtributo)) {
            $where['BNF_Oferta_Atributo_id'] = $idAtributo;
        }
        $this->tableGateway->update($data, $where);
    }

    public function updateXofertaCaducado($idOferta)
    {
        $data['FechaCaducado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Caducado';
        $this->tableGateway->update($data, array('BNF_Oferta_id' => $idOferta, 'EstadoCupon' => 'Generado'));
    }

    public function getCountCupon($empresa_id, $fechaInicio, $fechaFin, $opcion, $categoria = null, $id_cliente = null)
    {

        $select = new Select();
        $select->from(array('C' => 'BNF_Cupon'));
        if ($empresa_id != '') {
            $select->join(
                array('OEC' => 'BNF_OfertaEmpresaCliente'),
                'OEC.id = C.BNF_OfertaEmpresaCliente_id',
                array()
            );
            $select->where->equalTo('OEC.BNF_Empresa_id', $empresa_id);
        }
        if ($categoria != null) {
            $select->where
                ->equalTo('C.BNF_Categoria_id', $categoria);
        }
        if ($id_cliente != null) {
            $select->where
                ->equalTo('C.BNF_Cliente_id', $id_cliente);
        }
        if ($opcion == 1) {
            $select->where(
                "C.FechaGenerado BETWEEN '$fechaInicio' AND ADDDATE('$fechaFin', INTERVAL 1 DAY)"
            );
            $select->where->equalTo('EstadoCupon', 'Generado');
        } else {
            $select->where(
                "C.FechaRedimido BETWEEN '$fechaInicio' AND ADDDATE('$fechaFin', INTERVAL 1 DAY)"
            );
            $select->where->equalTo('EstadoCupon', 'Redimido');
        }
        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);
        if (!$resultSet) {
            return false;
        }
        return $resultSet->count();
    }

    public function getListIdClient()
    {
        $select = new Select();
        $select->from('BNF_Cupon');
        $select->where->isNotNull('BNF_Cliente_id');
        $select->group('BNF_Cliente_id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function hasCuponDescargas($oferta)
    {
        $select = new Select();
        $select->from('BNF_Cupon');
        $select->where->equalTo('BNF_Oferta_id', $oferta)
            ->and->isNotNull('FechaGenerado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function hasCuponDescargasByAtribuo($atributo)
    {
        $select = new Select();
        $select->from('BNF_Cupon');
        $select->where->equalTo('BNF_Oferta_Atributo_id', $atributo)
            ->and->isNotNull('FechaGenerado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function deleteCuponPorOferta($idOferta)
    {
        $data['FechaEliminado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Eliminado';
        $this->tableGateway->update($data, array('BNF_Oferta_id' => $idOferta));
    }

    public function deleteCuponByAtributo($atributo_id)
    {
        $data['FechaEliminado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Eliminado';
        $this->tableGateway->update($data, array('BNF_Oferta_Atributo_id' => $atributo_id));
    }
}
