<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:29 PM
 */

namespace Puntos\Model\Table;

use Puntos\Model\CampaniasP;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CampaniasPTable
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

    public function getCampaniasP($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
    }

    public function getCampaniasPByEmpresa($empresa)
    {
        $select = new Select();
        $select->from('BNF2_Campanias');
        $select->columns(array('id', 'NombreCampania'));
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Empresa.id", $empresa)
            ->AND->equalTo("BNF2_Campanias.Eliminado", 0)
            ->AND->equalTo("BNF2_Segmentos.Eliminado", 0);

        $select->group('BNF2_Campanias.id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getCampaniasPByEmpresaPersonalizada($empresa)
    {
        $fecha = date('Y-m-d');

        $select = new Select();
        $select->from('BNF2_Campanias');
        $select->columns(array('id', 'NombreCampania'));
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Empresa.id", $empresa)
            ->AND->equalTo("BNF2_Campanias.Eliminado", 0)
            ->AND->equalTo("BNF2_Campanias.EstadoCampania", "Publicado")
            ->AND->equalTo("BNF2_Campanias.TipoSegmento", "Personalizado")
            ->AND->equalTo("BNF2_Segmentos.Eliminado", 0);

        $select->where->literal("BNF2_Campanias.VigenciaFin >= '" . $fecha . "'");
        $select->where->literal("BNF2_Campanias.PresupuestoNegociado > 0");
        $select->where->literal("BNF2_Campanias.VigenciaInicio <= '" . $fecha . "'");
        $select->group('BNF2_Campanias.id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getPresupuestoAcumulado($id)
    {
        $select = new Select();
        $select->from(array("CMP" => 'BNF2_Campanias'));
        $select->columns(
            array(
                'PresupuestoAsignado' => new Expression(
                    "(SELECT 
                        IFNULL(SUM(BNF2_Asignacion_Puntos.CantidadPuntos), 0)
                    FROM
                        BNF2_Asignacion_Puntos
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Asignacion_Puntos.BNF2_Segmento_id = BNF2_Segmentos.id
                            INNER JOIN
                        BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id
                    WHERE
                        BNF2_Asignacion_Puntos.Eliminado = 0
                            AND BNF2_Campanias.id = CMP.id) + (SELECT 
                        IFNULL(SUM(CantidadPuntosUsados), 0)
                    FROM
                        (SELECT DISTINCT
                            (BNF_Cliente_id),
                                CantidadPuntosUsados,
                                BNF2_Segmento_id,
                                EstadoPuntos
                        FROM
                            BNF2_Asignacion_Puntos_Estado_Log
                        WHERE
                            EstadoPuntos = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF2_Asignacion_Puntos_Estado_Log
                            INNER JOIN
                        BNF2_Segmentos ON BNF2_Asignacion_Puntos_Estado_Log.BNF2_Segmento_id = BNF2_Segmentos.id
                            INNER JOIN
                        BNF2_Campanias ON BNF2_Segmentos.BNF2_Campania_id = BNF2_Campanias.id
                    WHERE
                        BNF2_Campanias.id = CMP.id)")
            )
        );
        $select->where->equalTo("CMP.id", $id);

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getCampaniasPByEmpresaActive($empresa)
    {
        $select = new Select();
        $select->from('BNF2_Campanias');
        $select->columns(array('id', 'NombreCampania'));
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Empresa.id", $empresa);
        $select->where->equalTo("BNF2_Campanias.Eliminado", 0);

        $select->group('BNF2_Campanias.id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getIfExist($id)
    {
        $id = (int)$id;
        try {
            $rowset = $this->tableGateway->select(array('id' => $id));
            $row = $rowset->current();
            if (!$row) {
                throw new \Exception("Could not find row $id");
            }
            return $row;
        } catch (\Exception $ex) {
            return 0;
        }
    }

    public function getListaDetallesCampania($order_by, $order, $empresa = "", $fecha = null)
    {
        $select = new Select();
        $select->from('BNF2_Campanias');
        $select->columns(
            array(
                'id',
                'NombreCampania',
                'VigenciaInicio',
                'VigenciaFin',
                'TipoSegmento',
                'EstadoCampania',
                'Comentario')
        );
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
            array('Presupuesto' => new Expression('Sum(Subtotal)'))
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa_id", $empresa);
        }

        if (!empty($fecha)) {
            $select->where->equalTo("FechaCampania", $fecha);
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF2_Campanias.FechaCreacion DESC");
        }

        $select->group('BNF2_Campanias.id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getEmpresasCliente($busqueda = null)
    {
        $select = new Select();
        $select->from('BNF2_Campanias');
        $select->columns(array());
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF2_Campanias_Empresas',
            'BNF2_Campanias.id = BNF2_Campanias_Empresas.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('id', "Empresa" => new Expression("CONCAT_WS(' - ', NombreComercial, RazonSocial, Ruc)"))
        );

        if (empty($busqueda)) {
            $select->join(
                'BNF2_Asignacion_Puntos',
                'BNF2_Segmentos.id = BNF2_Asignacion_Puntos.BNF2_Segmento_id',
                array()
            );
            $select->where->equalTo("BNF2_Campanias.Eliminado", 0);
            $select->where->equalTo("BNF2_Segmentos.Eliminado", 0);
            $select->where->equalTo("BNF2_Campanias_Empresas.Eliminado", 0);
        }
        $select->order("Empresa ASC");
        $select->group('BNF_Empresa.id');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveCampaniasP(CampaniasP $campaniasP)
    {
        $data = array(
            'NombreCampania' => $campaniasP->NombreCampania,
            'TipoSegmento' => $campaniasP->TipoSegmento,
            'FechaCampania' => $campaniasP->FechaCampania,
            'VigenciaInicio' => $campaniasP->VigenciaInicio,
            'VigenciaFin' => $campaniasP->VigenciaFin,
            'PresupuestoNegociado' => $campaniasP->PresupuestoNegociado,
            'ParametroAlerta' => $campaniasP->ParametroAlerta,
            'Comentario' => $campaniasP->Comentario,
            'Relacionado' => $campaniasP->Relacionado,
            'EstadoCampania' => $campaniasP->EstadoCampania,
            'Eliminado' => $campaniasP->Eliminado,
        );

        $id = (int)$campaniasP->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            $data['FechaActualizacion'] = date("Y-m-d H:i:s");
            if ($this->getCampaniasP($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('CampaniasP id does not exist');
            }
        }
        return $id;
    }

    public function deleteCampaniasP($id)
    {
        $data['EstadoCampania'] = "Eliminado";
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $data['Eliminado'] = 1;
        return $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getCampaniaFinalizadas()
    {
        $fecha = date('Y-m-d');
        $select = new Select();
        $select->from('BNF2_Campanias');
        $select->where->literal("VigenciaFin < '" . $fecha . "'")
            ->and->in('EstadoCampania', array('Publicado', 'Borrador'));
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEmpresasProveedora()
    {
        $select = new Select();
        $select->from('BNF2_Campanias');
        $select->columns(array());
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Segmentos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id',
            array()
        );
        $select->join(
            'BNF2_Oferta_Puntos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Oferta_Puntos.BNF_Empresa_id = BNF_Empresa.id',
            array('id', "Empresa" => new Expression("CONCAT_WS(' - ', NombreComercial, RazonSocial, Ruc)"))
        );

        $select->order("Empresa ASC");
        $select->group('BNF_Empresa.id');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getCampaniasPByEmpresaProv($empresa)
    {
        $select = new Select();
        $select->from('BNF2_Campanias');
        $select->columns(array('id', 'NombreCampania'));
        $select->join(
            'BNF2_Segmentos',
            'BNF2_Campanias.id = BNF2_Segmentos.BNF2_Campania_id',
            array()
        );
        $select->join(
            'BNF2_Oferta_Puntos_Segmentos',
            'BNF2_Segmentos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Segmento_id',
            array()
        );
        $select->join(
            'BNF2_Oferta_Puntos',
            'BNF2_Oferta_Puntos.id = BNF2_Oferta_Puntos_Segmentos.BNF2_Oferta_Puntos_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF2_Oferta_Puntos.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Empresa.id", $empresa)
            ->AND->equalTo("BNF2_Campanias.Eliminado", 0)
            ->AND->equalTo("BNF2_Segmentos.Eliminado", 0);

        $select->group('BNF2_Campanias.id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
