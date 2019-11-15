<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 05/07/16
 * Time: 06:01 PM
 */

namespace Cupon\Model\Table;

use Cupon\Model\CuponPuntos;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CuponPuntosTable
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

    public function getCuponPuntosRedimidos()
    {
        $resultSet = $this->tableGateway->select(array('EstadoCupon' => 'Redimido'));
        return $resultSet;
    }

    public function getCuponPuntos($id)
    {
        $id = (int)$id;
        $rowSet = $this->tableGateway->select(array('id' => $id));
        $row = $rowSet->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function searchCuponPuntos($codigo, $empresa = null)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->columns(
            array(
                'id' => 'id',
                'EstadoCupon',
                'PuntosUtilizados',
                'BNF2_Oferta_Puntos_id',
                'BNF2_Oferta_Puntos_Atributos_id',
                'FechaVigencia' => new Expression(
                    '(IFNULL(BNF2_Oferta_Puntos.FechaVigencia, BNF2_Oferta_Puntos_Atributos.FechaVigencia))'
                ),
                'PrecioVentaPublico' => new Expression(
                    '(IF(BNF2_Oferta_Puntos.PrecioVentaPublico = 0,
                    BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico, 
                    BNF2_Oferta_Puntos.PrecioVentaPublico))'
                )
            )
        );
        $select->join(
            'BNF2_Oferta_Puntos',
            'BNF2_Oferta_Puntos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_id',
            array('CondicionesUso', 'Titulo')
        );
        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'BNF2_Oferta_Puntos_Atributos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_Atributos_id',
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
                ->equalTo('BNF2_Oferta_Empresa_id', $empresa);
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();

        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCuponesOfertaPuntos($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select("BNF2_Oferta_Puntos_id = " . $id . " AND EstadoCupon != 'Eliminado'");
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("No hay cupones de la Oferta $id");
        }
        return $row;
    }

    public function getTotalCuponesPuntos($id)
    {
        $resultSet = $this->tableGateway->select("BNF2_Oferta_Puntos_id = " . $id);
        return $resultSet->count();
    }

    public function getTotalNoEliminadoPuntos($id)
    {
        $resultSet = $this->tableGateway->select("BNF2_Oferta_Puntos_id = " . $id . " AND EstadoCupon != 'Eliminado'");
        return $resultSet->count();
    }

    public function saveCuponPuntos(CuponPuntos $cuponPuntos)
    {
        $data = array(
            'BNF2_Oferta_Empresa_id' => $cuponPuntos->BNF2_Oferta_Empresa_id,
            'BNF_Cliente_id' => $cuponPuntos->BNF_Cliente_id,
            'EstadoCupon' => $cuponPuntos->EstadoCupon,
            'BNF2_Oferta_Puntos_id' => $cuponPuntos->BNF2_Oferta_Puntos_id,
            'BNF2_Oferta_Puntos_Atributos_id' => $cuponPuntos->BNF2_Oferta_Puntos_Atributos_id,
            'FechaEliminado' => $cuponPuntos->FechaEliminado,
            'FechaRedimido' => $cuponPuntos->FechaRedimido,
            'FechaGenerado' => $cuponPuntos->FechaGenerado,
            'FechaFinalizado' => $cuponPuntos->FechaFinalizado,
            'FechaCaducado' => $cuponPuntos->FechaCaducado,
        );
        $id = (int)$cuponPuntos->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
        } else {
            if ($this->getCuponPuntos($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('El Cupon no existe.');
            }
        }
        return $id;
    }

    public function getLastCuponPuntos($oferta, $ultimo = null, $atributoID = null)
    {
        $this->oferta = (int)$oferta;
        $this->ultima = (int)$ultimo;
        $this->atributo = (int)$atributoID;
        $rowset = $this->tableGateway->select(
            function (Select $select) {
                $select->where->equalTo('BNF2_Oferta_Puntos_id', $this->oferta);
                $select->where->notLike('EstadoCupon', 'Eliminado');
                $select->where->notLike('EstadoCupon', 'Generado');
                $select->where->notLike('EstadoCupon', 'Redimido');
                if ($this->ultima != 0) {
                    $select->where->lessThan('id', $this->ultima);
                }
                if ($this->atributo != 0) {
                    $select->where->equalTo('BNF2_Oferta_Puntos_Atributos_id', $this->atributo);
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

    public function getExpiredCuponPuntosGenerate($oferta)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->where->equalTo('BNF2_Oferta_Puntos_id', $oferta)
            ->and->equalTo('EstadoCupon', 'Generado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getExpiredCuponPuntosCreate($oferta, $atributo = null)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->where->equalTo('BNF2_Oferta_Puntos_id', $oferta)
            ->and->equalTo('EstadoCupon', 'Creado');

        if (!empty($atributo)) {
            $select->where->equalTo('BNF2_Oferta_Puntos_Atributos_id', $atributo);
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getExpiredCuponPuntosFinalized($oferta, $atributo = null)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->where->equalTo('BNF2_Oferta_Puntos_id', $oferta)
            ->and->equalTo('EstadoCupon', 'Generado');

        if (!empty($atributo)) {
            $select->where->equalTo('BNF2_Oferta_Puntos_Atributos_id', $atributo);
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function deleteCuponPuntos($idOferta, $idCupon)
    {
        $data['FechaEliminado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Eliminado';
        $this->tableGateway->update($data, array('id' => $idCupon, 'BNF2_Oferta_Puntos_id' => $idOferta));
    }

    public function redimirCuponPuntos($idCupon)
    {
        $data['FechaRedimido'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Redimido';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function anularCuponPuntos($idCupon)
    {
        $data['FechaAnulado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Anulado';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function porPagarCuponPuntos($idCupon)
    {
        $data['FechaPorPagar'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Por Pagar';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function pagadoCuponPuntos($idCupon)
    {
        $data['FechaPagado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Pagado';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function standByCuponPuntos($idCupon)
    {
        $data['FechaStandBy'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Stand By';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function generadoCuponPuntos($idCupon)
    {
        $data['FechaStandBy'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Generado';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function caducadoCuponPuntos($idCupon)
    {
        $data['FechaCaducado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Caducado';
        $this->tableGateway->update($data, array('id' => $idCupon));
    }

    public function updateOfertaFinalizado($idOferta)
    {
        $data['FechaFinalizado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Finalizado';
        $this->tableGateway->update($data, array('BNF2_Oferta_Puntos_id' => $idOferta, 'EstadoCupon' => 'Creado'));
    }

    public function updateOfertaCaducado($idOferta)
    {
        $data['FechaCaducado'] = date("Y-m-d H:i:s");
        $data['EstadoCupon'] = 'Caducado';
        $this->tableGateway->update($data, array('BNF2_Oferta_Puntos_id' => $idOferta, 'EstadoCupon' => 'Generado'));
    }

    public function countCuponPuntos(
        $empresa_id,
        $fechaInicio,
        $fechaFin,
        $opcion,
        $categoria = null,
        $id_cliente = null
    )
    {
        $select = new Select();
        $select->from(array('C' => 'BNF2_Cupon_Puntos'));
        if ($empresa_id != '') {
            $select->join(
                array('OEC' => 'BNF2_OfertaEmpresaCliente_Puntos'),
                'OEC.id = C.BNF2_OfertaEmpresaCliente_id',
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
        $select->from('BNF2_Cupon_Puntos');
        $select->where->isNotNull('BNF_Cliente_id');
        $select->group('BNF_Cliente_id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getListaCuponesPuntos($order_by, $order, $empresa, $campania, $oferta, $estado, $desde, $hasta, $codigo)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->columns(
            array(
                'id' => new Expression('DISTINCT BNF2_Cupon_Puntos.id'),
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
            'BNF2_Oferta_Puntos',
            'BNF2_Oferta_Puntos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_id',
            array(
                'Oferta' => new Expression(
                    'IFNULL(BNF2_Oferta_Puntos_Atributos.NombreAtributo , Titulo)'
                ),
                'PrecioVentaPublico' => new Expression(
                    "IF(BNF2_Oferta_Puntos.TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.PrecioVentaPublico,
                        BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico)"
                ),
                'PrecioBeneficio' => new Expression(
                    "IF(BNF2_Oferta_Puntos.TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.PrecioBeneficio,
                        BNF2_Oferta_Puntos_Atributos.PrecioBeneficio)"
                )
            )
        );

        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'BNF2_Oferta_Puntos_Atributos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_Atributos_id',
            array(),
            'left'
        );

        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF2_Cupon_Puntos.BNF2_Oferta_Empresa_id',
            array('Empresa' => 'NombreComercial')
        );

        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id',
            array()
        );

        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id',
            array()
        );

        $select->join(
            'BNF2_Campanias',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
            array('Campania' => 'NombreCampania')
        );

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF2_Cupon_Puntos.id DESC");
        }

        $select->where->equalTo('EstadoCupon', $estado);
        $select->where->equalTo('BNF2_Segmentos.Eliminado', 0);
        $select->where->equalTo('BNF2_Oferta_Puntos_Segmentos.Eliminado', 0);

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
            $select->where->equalTo('BNF2_Cupon_Puntos.BNF_Empresa_id', $empresa);
        }

        if (!empty($oferta)) {
            $select->where->equalTo('BNF2_Oferta_Puntos.id', $oferta);
        }

        if (!empty($campania)) {
            $select->where->equalTo('BNF2_Campanias.id', $campania);
        }

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function reporteCuponPuntos($empresa, $campania, $oferta, $estado, $desde, $hasta, $codigo)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->columns(
            array(
                'id' => new Expression('DISTINCT BNF2_Cupon_Puntos.id'),
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
            'BNF2_Oferta_Puntos',
            'BNF2_Oferta_Puntos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_id',
            array(
                'Oferta' => new Expression(
                    'IFNULL(BNF2_Oferta_Puntos_Atributos.NombreAtributo , Titulo)'
                ),
                'PrecioVentaPublico' => new Expression(
                    "IF(BNF2_Oferta_Puntos.TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.PrecioVentaPublico,
                        BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico)"
                ),
                'PrecioBeneficio' => new Expression(
                    "IF(BNF2_Oferta_Puntos.TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.PrecioBeneficio,
                        BNF2_Oferta_Puntos_Atributos.PrecioBeneficio)"
                )
            )
        );

        $select->join(
            'BNF2_Oferta_Puntos_Atributos',
            'BNF2_Oferta_Puntos_Atributos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_Atributos_id',
            array(),
            'left'
        );

        $select->join(
            'BNF_Empresa',
            'BNF_Empresa.id = BNF2_Cupon_Puntos.BNF2_Oferta_Empresa_id',
            array('Empresa' => 'NombreComercial')
        );

        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id',
            array()
        );

        $select->join(
            'BNF2_Segmentos',
            'BNF2_Segmentos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id',
            array()
        );

        $select->join(
            'BNF2_Campanias',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
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
            $select->where->equalTo('BNF2_Cupon_Puntos.BNF_Empresa_id', $empresa);
        }

        if (!empty($oferta)) {
            $select->where->equalTo('BNF2_Oferta_Puntos.id', $oferta);
        }

        if (!empty($campania)) {
            $select->where->equalTo('BNF2_Campanias.id', $campania);
        }

        $select->order("BNF2_Cupon_Puntos.id DESC");

        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }


    public function getEstatus($estado)
    {

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

        return $fechaEstado;
    }

    public function getListaCuponesPuntosPorEstado($order_by, $order, $empresa, $estado, $desde, $hasta)
    {
            $select = new Select();
            $select->from('BNF2_Cupon_Puntos');
            $select->columns(
                array(
                    'id' => new Expression('DISTINCT BNF2_Cupon_Puntos.id'),
                    'CodigoCupon',
                    'BNF_Empresa_id',
                    'ComentarioUno' => new \Zend\Db\Sql\Expression("
                                     (SELECT BNF2_Cupon_Puntos_Log.comentario_uno
                                     FROM BNF2_Cupon_Puntos_Log
                                     WHERE   BNF2_Cupon_Puntos_Log.BNF2_Cupon_Puntos_id = BNF2_Cupon_Puntos.id
                                     AND  BNF2_Cupon_Puntos_Log.EstadoCupon = BNF2_Cupon_Puntos.EstadoCupon
                                     ORDER BY BNF2_Cupon_Puntos_Log.FechaCreacion DESC LIMIT 1)"),

                    'ComentarioDos' => new \Zend\Db\Sql\Expression("
                                     (SELECT BNF2_Cupon_Puntos_Log.comentario_dos
                                     FROM BNF2_Cupon_Puntos_Log
                                     WHERE   BNF2_Cupon_Puntos_Log.BNF2_Cupon_Puntos_id = BNF2_Cupon_Puntos.id
                                     AND  BNF2_Cupon_Puntos_Log.EstadoCupon = BNF2_Cupon_Puntos.EstadoCupon
                                     ORDER BY BNF2_Cupon_Puntos_Log.FechaCreacion DESC LIMIT 1)"),
                    'EstadoCupon' => 'EstadoCupon',
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
                'BNF2_Oferta_Puntos',
                'BNF2_Oferta_Puntos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_id',
                array(
                    'Oferta' => new Expression(
                        'IFNULL(BNF2_Oferta_Puntos_Atributos.NombreAtributo , Titulo)'
                    ),
                    'PrecioVentaPublico' => new Expression(
                        "IF(BNF2_Oferta_Puntos.TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.PrecioVentaPublico,
                        BNF2_Oferta_Puntos_Atributos.PrecioVentaPublico)"
                    ),
                    'PrecioBeneficio' => new Expression(
                        "IF(BNF2_Oferta_Puntos.TipoPrecio = 'Unico',
                        BNF2_Oferta_Puntos.PrecioBeneficio,
                        BNF2_Oferta_Puntos_Atributos.PrecioBeneficio)"
                    )
                )
            );

            $select->join(
                'BNF2_Oferta_Puntos_Atributos',
                'BNF2_Oferta_Puntos_Atributos.id = BNF2_Cupon_Puntos.BNF2_Oferta_Puntos_Atributos_id',
                array(),
                'left'
            );

            $select->join(
                'BNF_Empresa',
                'BNF_Empresa.id = BNF2_Cupon_Puntos.BNF2_Oferta_Empresa_id',
                array('Empresa' => 'NombreComercial')
            );

            $select->join(
                'BNF2_Oferta_Puntos_Segmentos',
                'BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id = BNF2_Oferta_Puntos.id',
                array()
            );

            $select->join(
                'BNF2_Segmentos',
                'BNF2_Segmentos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id',
                array()
            );

            $select->join(
                'BNF2_Campanias',
                'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
                array('Campania' => 'NombreCampania')
            );

            if (isset($order_by) && $order_by != "" && $order_by != 'id') {
                $select->order($order_by . ' ' . $order);
            } else {
                $select->order("BNF2_Cupon_Puntos.id DESC");
            }

            $select->where(array('EstadoCupon' => $estado));
            $select->where->equalTo('BNF2_Segmentos.Eliminado', 0);
            $select->where->equalTo('BNF2_Oferta_Puntos_Segmentos.Eliminado', 0);

            if (count($estado) == 1) {
                $fechaEstado = $this->getEstatus($estado[0]);
                $select->where->addPredicate(
                    new \Zend\Db\Sql\Predicate\Expression("date($fechaEstado) BETWEEN '$desde' AND '$hasta'")
                );
            } elseif (count($estado) > 1) {
                $fechaEstado = $this->getEstatus($estado[0]);
                $fechaEstadoTwo = $this->getEstatus($estado[1]);
                $select->where->addPredicate(
                    new \Zend\Db\Sql\Predicate\Expression("date($fechaEstado) BETWEEN '$desde' AND '$hasta'")
                );
            }
            if (!empty($empresa)) {
//                /var_dump($empresa);exit;

                $select->where->equalTo('BNF2_Cupon_Puntos.BNF2_Oferta_Empresa_id', $empresa);
            }
            //   echo $select->getSqlString();exit;
            $resultSet = $this->tableGateway->selectWith($select);
//            var_dump($resultSet->buffer());exit;
            return $resultSet->buffer();

    }


    public function getCuponPuntosDescargados($oferta_id, $atributo_id = 0)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->where->equalTo('BNF2_Oferta_Puntos_id', $oferta_id);
        $select->where->isNotNull('FechaGenerado');
        if ($atributo_id != 0) {
            $select->where->equalTo('BNF2_Oferta_Puntos_Atributos_id', $atributo_id);
        }
        //echo str_replace('"', '', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getCuponesForOferta($oferta_id)
    {
        $select = new Select();
        $select->from(array('CP' => 'BNF2_Cupon_Puntos'));
        $select->columns(
            array(
                '*',
                'PrecioBeneficio' => new Expression("(
                    IF((SELECT TipoPrecio FROM BNF2_Oferta_Puntos WHERE id = CP.BNF2_Oferta_Puntos_id) = 'Unico',
                    (SELECT PrecioBeneficio FROM BNF2_Oferta_Puntos WHERE id = CP.BNF2_Oferta_Puntos_id),
                    (SELECT PrecioBeneficio FROM BNF2_Oferta_Puntos_Atributos WHERE id = CP.BNF2_Oferta_Puntos_Atributos_id))
                )")
            )
        );

        $select->where->equalTo('CP.BNF2_Oferta_Puntos_id', $oferta_id);
        $select->where->isNotNull('CP.FechaGenerado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getCuponesByAtributo($id)
    {
        $select = new Select();
        $select->from(array('CP' => 'BNF2_Cupon_Puntos'));
        $select->columns(
            array(
                '*',
                'PrecioBeneficio' => new Expression("(
                    IF((SELECT TipoPrecio FROM BNF2_Oferta_Puntos WHERE id = CP.BNF2_Oferta_Puntos_id) = 'Unico',
                    (SELECT PrecioBeneficio FROM BNF2_Oferta_Puntos WHERE id = CP.BNF2_Oferta_Puntos_id),
                    (SELECT PrecioBeneficio FROM BNF2_Oferta_Puntos_Atributos WHERE id = CP.BNF2_Oferta_Puntos_Atributos_id))
                )")
            )
        );

        $select->where->equalTo('CP.BNF2_Oferta_Puntos_Atributos_id', $id);
        $select->where->isNotNull('CP.FechaGenerado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function hasCuponPuntosDescargas($oferta)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->where->equalTo('BNF2_Oferta_Puntos_id', $oferta)
            ->and->isNotNull('FechaGenerado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function hasCuponPuntosDescargasByAtributo($atributo)
    {
        $select = new Select();
        $select->from('BNF2_Cupon_Puntos');
        $select->where->equalTo('BNF2_Oferta_Puntos_Atributos_id', $atributo)
            ->and->isNotNull('FechaGenerado');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }
}
