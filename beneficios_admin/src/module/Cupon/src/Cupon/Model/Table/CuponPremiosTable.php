<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 05/07/16
 * Time: 06:01 PM
 */

namespace Cupon\Model\Table;

use Cupon\Model\CuponPremios;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CuponPremiosTable
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

    public function getCuponPremiosRedimidos()
    {
        $resultSet = $this->tableGateway->select(array('EstadoCupon' => 'Redimido'));
        return $resultSet;
    }

    public function getCuponPremios($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function searchCuponPremios($codigo, $empresa = null)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->columns(
            array(
                'id' => 'id',
                'EstadoCupon',
                'PremiosUtilizados',
                'BNF3_Oferta_Premios_id',
                'BNF3_Oferta_Premios_Atributos_id',
                'FechaVigencia' => new Expression(
                    '(IFNULL(BNF3_Oferta_Premios.FechaVigencia, BNF3_Oferta_Premios_Atributos.FechaVigencia))'
                ),
                'PrecioVentaPublico' => new Expression(
                    '(IF(BNF3_Oferta_Premios.PrecioVentaPublico = 0,
                    BNF3_Oferta_Premios_Atributos.PrecioVentaPublico, 
                    BNF3_Oferta_Premios.PrecioVentaPublico))'
                )
            )
        );
        $select->join(
            'BNF3_Oferta_Premios',
            'BNF3_Oferta_Premios.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_id',
            array('CondicionesUso', 'Titulo')
        );
        $select->join(
            'BNF3_Oferta_Premios_Atributos',
            'BNF3_Oferta_Premios_Atributos.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_Atributos_id',
            array(),
            'left'
        );

        $select->where
            ->equalTo('CodigoCupon', $codigo)
            ->and
            ->notEqualTo('EstadoCupon', 'Eliminado')
            ->and
            ->notEqualTo('EstadoCupon', 'Creado');

        if ($empresa != null) {
            $select->where
                ->equalTo('BNF3_Oferta_Empresa_id', $empresa);
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCuponesOfertaPremios($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select("BNF3_Oferta_Premios_id = " . $id . " AND EstadoCupon != 'Eliminado'");
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No hay cupones de la Oferta $id");
        }
        return $row;
    }

    public function getTotalCuponesPremios($id)
    {
        $resultSet = $this->tableGateway->select("BNF3_Oferta_Premios_id = " . $id);
        return $resultSet->count();
    }

    public function getTotalNoEliminadoPremios($id)
    {
        $resultSet = $this->tableGateway->select("BNF3_Oferta_Premios_id = " . $id . " AND EstadoCupon != 'Eliminado'");
        return $resultSet->count();
    }

    public function saveCuponPremios(CuponPremios $cuponPremios)
    {
        $data = array(
            'BNF3_Oferta_Empresa_id' => $cuponPremios->BNF3_Oferta_Empresa_id,
            'BNF_Cliente_id' => $cuponPremios->BNF_Cliente_id,
            'EstadoCupon' => $cuponPremios->EstadoCupon,
            'BNF3_Oferta_Premios_id' => $cuponPremios->BNF3_Oferta_Premios_id,
            'BNF3_Oferta_Premios_Atributos_id' => $cuponPremios->BNF3_Oferta_Premios_Atributos_id,
            'FechaEliminado' => $cuponPremios->FechaEliminado,
            'FechaRedimido' => $cuponPremios->FechaRedimido,
            'FechaGenerado' => $cuponPremios->FechaGenerado,
            'FechaFinalizado' => $cuponPremios->FechaFinalizado,
            'FechaCaducado' => $cuponPremios->FechaCaducado,
        );
        $id = (int)$cuponPremios->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCuponPremios($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('El Cupon no existe.');
            }
        }
        return $id;
    }

    public function getLastCuponPremios($oferta, $ultimo = null, $atributoID = null)
    {
        $this->oferta = (int)$oferta;
        $this->ultima = (int)$ultimo;
        $this->atributo = (int)$atributoID;
        $rowset = $this->tableGateway->select(
            function (Select $select) {
                $select->where->equalTo('BNF3_Oferta_Premios_id', $this->oferta);
                $select->where->notLike('EstadoCupon', 'Eliminado');
                $select->where->notLike('EstadoCupon', 'Generado');
                $select->where->notLike('EstadoCupon', 'Redimido');
                if ($this->ultima != 0) {
                    $select->where->lessThan('id', $this->ultima);
                }
                if ($this->atributo != 0) {
                    $select->where->equalTo('BNF3_Oferta_Premios_Atributos_id', $this->atributo);
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

    public function getExpiredCuponPremiosGenerate($oferta)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->where->equalTo('BNF3_Oferta_Premios_id', $oferta)
            ->and->equalTo('EstadoCupon', 'Generado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getExpiredCuponPremiosCreate($oferta, $atributo = null)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->where->equalTo('BNF3_Oferta_Premios_id', $oferta)
            ->and->equalTo('EstadoCupon', 'Creado');

        if (!empty($atributo)) {
            $select->where->equalTo('BNF3_Oferta_Premios_Atributos_id', $atributo);
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getExpiredCuponPremiosFinalized($oferta, $atributo = null)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->where->equalTo('BNF3_Oferta_Premios_id', $oferta)
            ->and->equalTo('EstadoCupon', 'Generado');

        if (!empty($atributo)) {
            $select->where->equalTo('BNF3_Oferta_Premios_Atributos_id', $atributo);
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function deleteCuponPremios($idOferta, $idCupon)
    {
        $data['FechaEliminado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Eliminado';
        $this->tableGateway->update($data, array('id' => $idCupon, 'BNF3_Oferta_Premios_id' => $idOferta));
    }

    public function redimirCuponPremios($idCupon)
    {
        $data['FechaRedimido'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Redimido';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function anularCuponPremios($idCupon)
    {
        $data['FechaAnulado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Anulado';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function porPagarCuponPremios($idCupon)
    {
        $data['FechaPorPagar'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Por Pagar';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function pagadoCuponPremios($idCupon)
    {
        $data['FechaPagado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Pagado';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function standByCuponPremios($idCupon)
    {
        $data['FechaStandBy'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Stand By';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function generadoCuponPremios($idCupon)
    {
        $data['FechaStandBy'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Generado';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }
    public function caducadoCuponPremios($idCupon)
    {
        $data['FechaCaducado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Caducado';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function updateOfertaFinalizado($idOferta)
    {
        $data['FechaFinalizado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Finalizado';
        $this->tableGateway->update($data, array('BNF3_Oferta_Premios_id' => $idOferta, 'EstadoCupon' => 'Creado'));
    }

    public function updateOfertaCaducado($idOferta)
    {
        $data['FechaCaducado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Caducado';
        $this->tableGateway->update($data, array('BNF3_Oferta_Premios_id' => $idOferta, 'EstadoCupon' => 'Generado'));
    }

    public function countCuponPremios(
        $empresa_id,
        $fechaInicio,
        $fechaFin,
        $opcion,
        $categoria = null,
        $id_cliente = null
    )
    {
        $select = new Select();
        $select->from(array('C' => 'BNF3_Cupon_Premios'));
        if ($empresa_id != '') {
            $select->join(
                array('OEC' => 'BNF3_OfertaEmpresaCliente_Premios'),
                'OEC.id = C.BNF3_OfertaEmpresaCliente_id',
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
        $select->from('BNF3_Cupon_Premios');
        $select->where->isNotNull('BNF_Cliente_id');
        $select->group('BNF_Cliente_id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getListaCuponesPremios($order_by, $order, $empresa, $campania, $oferta, $estado, $desde, $hasta, $codigo)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->columns(
            array(
                'id' => new Expression('DISTINCT BNF3_Cupon_Premios.id'),
                'CodigoCupon',
                'EstadoCupon',
                'UltimaActualizacion' => new Expression(
                    "CASE EstadoCupon
                        WHEN 'Generado' THEN DATE(FechaGenerado)
                        WHEN 'Redimido' THEN DATE(FechaRedimido)
                        WHEN 'Por Pagar' THEN DATE(FechaPorPagar)
                        WHEN 'Pagado' THEN DATE(FechaPagado)
                        WHEN 'Stand By' THEN DATE(FechaStandBy)
                        WHEN 'Anulado' THEN DATE(FechaAnulado)
                        WHEN 'Caducado' THEN DATE(FechaCaducado)
                    END"
                ),
            )
        );
        $select->join(
            'BNF3_Oferta_Premios',
            'BNF3_Oferta_Premios.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_id',
            array(
                'Oferta' => new Expression(
                    'IFNULL(BNF3_Oferta_Premios_Atributos.NombreAtributo , Titulo)'
                ),
                'PrecioVentaPublico' => new Expression(
                    "IF(BNF3_Oferta_Premios.TipoPrecio = 'Unico',
                        BNF3_Oferta_Premios.PrecioVentaPublico,
                        BNF3_Oferta_Premios_Atributos.PrecioVentaPublico)"
                ),
                'PrecioBeneficio' => new Expression(
                    "IF(BNF3_Oferta_Premios.TipoPrecio = 'Unico',
                        BNF3_Oferta_Premios.PrecioBeneficio,
                        BNF3_Oferta_Premios_Atributos.PrecioBeneficio)"
                )
            )
        );

        $select->join(
            'BNF3_Oferta_Premios_Atributos',
            'BNF3_Oferta_Premios_Atributos.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_Atributos_id',
            array(),
            'left'
        );

        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF3_Cupon_Premios.BNF3_Oferta_Empresa_id',
            array('Empresa' => 'NombreComercial')
        );

        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id',
            array()
        );

        $select->join(
            'BNF3_Segmentos',
            'BNF3_Segmentos.id = BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id',
            array()
        );

        $select->join(
            'BNF3_Campanias',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array('Campania' => 'NombreCampania')
        );

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF3_Cupon_Premios.id DESC");
        }

        $select->where->equalTo('EstadoCupon', $estado);

        $fechaEstado = "";
        if ($estado == "Generado") {
            $fechaEstado = "FechaGenerado";
        } elseif ($estado == "Redimido") {
            $fechaEstado = "FechaRedimido";
        } elseif ($estado == "Por Pagar") {
            $fechaEstado = "FechaPorPagar";
        } elseif ($estado == "Pagado") {
            $fechaEstado = "FechaPagado";
        } elseif ($estado == "Stand By") {
            $fechaEstado = "FechaStandBy";
        } elseif ($estado == "Anulado") {
            $fechaEstado = "FechaAnulado";
        } elseif ($estado == "Caducado") {
            $fechaEstado = "FechaCaducado";
        }

        $select->where->addPredicate(
            new \Zend\Db\Sql\Predicate\Expression("date($fechaEstado) BETWEEN '$desde' AND '$hasta'")
        );

        if (!empty($codigo)) {
            $select->where->equalTo('CodigoCupon', $codigo);
        }

        if (!empty($empresa)) {
            $select->where->equalTo('BNF3_Cupon_Premios.BNF_Empresa_id', $empresa);
        }

        if (!empty($oferta)) {
            $select->where->equalTo('BNF3_Oferta_Premios.id', $oferta);
        }

        if (!empty($campania)) {
            $select->where->equalTo('BNF3_Campanias.id', $campania);
        }

        $select->where->equalTo('BNF3_Campanias.Eliminado', 0);
        $select->where->equalTo('BNF3_Segmentos.Eliminado', 0);
        $select->where->equalTo('BNF3_Oferta_Premios_Segmentos.Eliminado', 0);

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function reporteCuponPremios($empresa, $campania, $oferta, $estado, $desde, $hasta, $codigo)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->columns(
            array(
                'id' => new Expression('DISTINCT BNF3_Cupon_Premios.id'),
                'CodigoCupon',
                'EstadoCupon',
                'UltimaActualizacion' => new Expression(
                    "CASE EstadoCupon
                        WHEN 'Generado' THEN DATE(FechaGenerado)
                        WHEN 'Redimido' THEN DATE(FechaRedimido)
                        WHEN 'Por Pagar' THEN DATE(FechaPorPagar)
                        WHEN 'Pagado' THEN DATE(FechaPagado)
                        WHEN 'Stand By' THEN DATE(FechaStandBy)
                        WHEN 'Anulado' THEN DATE(FechaAnulado)
                        WHEN 'Caducado' THEN DATE(FechaCaducado)
                    END"
                ),
            )
        );
        $select->join(
            'BNF3_Oferta_Premios',
            'BNF3_Oferta_Premios.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_id',
            array(
                'Oferta' => new Expression(
                    'IFNULL(BNF3_Oferta_Premios_Atributos.NombreAtributo , Titulo)'
                ),
                'PrecioVentaPublico' => new Expression(
                    "IF(BNF3_Oferta_Premios.TipoPrecio = 'Unico',
                        BNF3_Oferta_Premios.PrecioVentaPublico,
                        BNF3_Oferta_Premios_Atributos.PrecioVentaPublico)"
                ),
                'PrecioBeneficio' => new Expression(
                    "IF(BNF3_Oferta_Premios.TipoPrecio = 'Unico',
                        BNF3_Oferta_Premios.PrecioBeneficio,
                        BNF3_Oferta_Premios_Atributos.PrecioBeneficio)"
                )
            )
        );

        $select->join(
            'BNF3_Oferta_Premios_Atributos',
            'BNF3_Oferta_Premios_Atributos.id = BNF3_Cupon_Premios.BNF3_Oferta_Premios_Atributos_id',
            array(),
            'left'
        );

        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF3_Cupon_Premios.BNF3_Oferta_Empresa_id',
            array('Empresa' => 'NombreComercial')
        );

        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id = BNF3_Oferta_Premios.id',
            array()
        );

        $select->join(
            'BNF3_Segmentos',
            'BNF3_Segmentos.id = BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id',
            array()
        );

        $select->join(
            'BNF3_Campanias',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array('Campania' => 'NombreCampania')
        );

        $select->where->equalTo('EstadoCupon', $estado);

        $fechaEstado = "";
        if ($estado == "Generado") {
            $fechaEstado = "FechaGenerado";
        } elseif ($estado == "Redimido") {
            $fechaEstado = "FechaRedimido";
        } elseif ($estado == "Por Pagar") {
            $fechaEstado = "FechaPorPagar";
        } elseif ($estado == "Pagado") {
            $fechaEstado = "FechaPagado";
        } elseif ($estado == "Stand By") {
            $fechaEstado = "FechaStandBy";
        } elseif ($estado == "Anulado") {
            $fechaEstado = "FechaAnulado";
        } elseif ($estado == "Caducado") {
            $fechaEstado = "FechaCaducado";
        }

        $select->where->addPredicate(
            new \Zend\Db\Sql\Predicate\Expression("date($fechaEstado) BETWEEN '$desde' AND '$hasta'")
        );

        if (!empty($codigo)) {
            $select->where->equalTo('CodigoCupon', $codigo);
        }

        if (!empty($empresa)) {
            $select->where->equalTo('BNF3_Cupon_Premios.BNF_Empresa_id', $empresa);
        }

        if (!empty($oferta)) {
            $select->where->equalTo('BNF3_Oferta_Premios.id', $oferta);
        }

        if (!empty($campania)) {
            $select->where->equalTo('BNF3_Campanias.id', $campania);
        }

        $select->order("BNF3_Cupon_Premios.id DESC");

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getCuponPremiosDescargados($oferta_id, $atributo_id = 0)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->where->equalTo('BNF3_Oferta_Premios_id', $oferta_id);
        $select->where->isNotNull('FechaGenerado');
        if ($atributo_id != 0) {
            $select->where->equalTo('BNF3_Oferta_Premios_Atributos_id', $atributo_id);
        }
        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getCuponesForOferta($oferta_id)
    {
        $select = new Select();
        $select->from(array('CP' => 'BNF3_Cupon_Premios'));
        $select->columns(
            array(
                '*',
                'PrecioBeneficio' => new Expression("(
                    IF((SELECT TipoPrecio FROM BNF3_Oferta_Premios WHERE id = CP.BNF3_Oferta_Premios_id) = 'Unico',
                    (SELECT PrecioBeneficio FROM BNF3_Oferta_Premios WHERE id = CP.BNF3_Oferta_Premios_id),
                    (SELECT PrecioBeneficio FROM BNF3_Oferta_Premios_Atributos WHERE id = CP.BNF3_Oferta_Premios_Atributos_id))
                )")
            )
        );

        $select->where->equalTo('CP.BNF3_Oferta_Premios_id', $oferta_id);
        $select->where->isNotNull('CP.FechaGenerado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getCuponesByAtributo($id)
    {
        $select = new Select();
        $select->from(array('CP' => 'BNF3_Cupon_Premios'));
        $select->columns(
            array(
                '*',
                'PrecioBeneficio' => new Expression("(
                    IF((SELECT TipoPrecio FROM BNF3_Oferta_Premios WHERE id = CP.BNF3_Oferta_Premios_id) = 'Unico',
                    (SELECT PrecioBeneficio FROM BNF3_Oferta_Premios WHERE id = CP.BNF3_Oferta_Premios_id),
                    (SELECT PrecioBeneficio FROM BNF3_Oferta_Premios_Atributos WHERE id = CP.BNF3_Oferta_Premios_Atributos_id))
                )")
            )
        );

        $select->where->equalTo('CP.BNF3_Oferta_Premios_Atributos_id', $id);
        $select->where->isNotNull('CP.FechaGenerado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function hasCuponPremiosDescargas($oferta)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->where->equalTo('BNF3_Oferta_Premios_id', $oferta)
            ->and->isNotNull('FechaGenerado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function hasCuponPremiosDescargasByAtributo($atributo)
    {
        $select = new Select();
        $select->from('BNF3_Cupon_Premios');
        $select->where->equalTo('BNF3_Oferta_Premios_Atributos_id', $atributo)
            ->and->isNotNull('FechaGenerado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }
}
