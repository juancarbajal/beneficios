<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 31/08/15
 * Time: 07:29 PM
 */

namespace Premios\Model\Table;

use Premios\Model\CampaniasP;
use Premios\Model\CampaniasPremios;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;

class CampaniasPremiosTable
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
        $select->from('BNF3_Campanias');
        $select->columns(array('id', 'NombreCampania'));
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Empresa.id", $empresa)
            ->AND->equalTo("BNF3_Campanias.Eliminado", 0)
            ->AND->equalTo("BNF3_Segmentos.Eliminado", 0);

        $select->group('BNF3_Campanias.id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getPresupuestoAcumulado($id)
    {
        $select = new Select();
        $select->from(array("CMP" => 'BNF3_Campanias'));
        $select->columns(
            array(
                'PresupuestoAsignado' => new Expression(
                    "(SELECT 
                        IFNULL(SUM(BNF3_Asignacion_Premios.CantidadPremios), 0)
                    FROM
                        BNF3_Asignacion_Premios
                            INNER JOIN
                        BNF3_Segmentos ON BNF3_Asignacion_Premios.BNF3_Segmento_id = BNF3_Segmentos.id
                            INNER JOIN
                        BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id
                    WHERE
                        BNF3_Asignacion_Premios.Eliminado = 0
                            AND BNF3_Campanias.id = CMP.id) + (SELECT 
                        IFNULL(SUM(CantidadPremiosUsados), 0)
                    FROM
                        (SELECT DISTINCT
                            (BNF_Cliente_id),
                                CantidadPremiosUsados,
                                BNF3_Segmento_id,
                                EstadoPremios
                        FROM
                            BNF3_Asignacion_Premios_Estado_Log
                        WHERE
                            EstadoPremios = 'Cancelado'
                        GROUP BY BNF_Cliente_id
                        ORDER BY BNF_Cliente_id DESC) AS BNF3_Asignacion_Premios_Estado_Log
                            INNER JOIN
                        BNF3_Segmentos ON BNF3_Asignacion_Premios_Estado_Log.BNF3_Segmento_id = BNF3_Segmentos.id
                            INNER JOIN
                        BNF3_Campanias ON BNF3_Segmentos.BNF3_Campania_id = BNF3_Campanias.id
                    WHERE
                        BNF3_Campanias.id = CMP.id)")
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
        $select->from('BNF3_Campanias');
        $select->columns(array('id', 'NombreCampania'));
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Empresa.id", $empresa);
        $select->where->equalTo("BNF3_Campanias.Eliminado", 0);

        $select->group('BNF3_Campanias.id');
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
        $select->from('BNF3_Campanias');
        $select->columns(array('id', 'NombreCampania', 'VigenciaInicio', 'VigenciaFin', 'TipoSegmento', 'EstadoCampania'));
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array('Presupuesto' => new Expression('Sum(Subtotal)'))
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
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
            $select->order("BNF3_Campanias.FechaCreacion DESC");
        }

        $select->group('BNF3_Campanias.id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getEmpresasCliente($busqueda = null)
    {
        $select = new Select();
        $select->from('BNF3_Campanias');
        $select->columns(array());
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('id', "Empresa" => new Expression("CONCAT_WS(' - ', NombreComercial, RazonSocial, Ruc)"))
        );

        if (empty($busqueda)) {
            $select->join(
                'BNF3_Asignacion_Premios',
                'BNF3_Segmentos.id = BNF3_Asignacion_Premios.BNF3_Segmento_id',
                array()
            );
            $select->where->equalTo("BNF3_Campanias.Eliminado", 0);
            $select->where->equalTo("BNF3_Segmentos.Eliminado", 0);
            $select->where->equalTo("BNF3_Campanias_Empresas.Eliminado", 0);
        }
        $select->order("Empresa ASC");
        $select->group('BNF_Empresa.id');

        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getReporte($empresa = "", $fecha = null)
    {
        $select = new Select();
        $select->from('BNF3_Campanias');
        $select->columns(array('id', 'NombreCampania', 'VigenciaInicio', 'VigenciaFin', 'TipoSegmento', 'Eliminado'));
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array('Presupuesto' => new Expression('Sum(Subtotal)'))
        );
        $select->join(
            'BNF3_Campanias_Empresas',
            'BNF3_Campanias.id = BNF3_Campanias_Empresas.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Campanias_Empresas.BNF_Empresa_id = BNF_Empresa.id',
            array('Empresa' => 'NombreComercial')
        );

        $select->where->equalTo("BNF3_Segmentos.Eliminado", 0);

        if (!empty($empresa)) {
            $select->where->equalTo("BNF_Empresa.id", $empresa);
        }

        if (!empty($fecha)) {
            $select->where->equalTo("BNF3_Campanias.FechaCampania", $fecha);
        }

        $select->order("BNF3_Campanias.id DESC");

        $select->group('BNF3_Campanias.id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function saveCampaniasP(CampaniasPremios $campaniasP)
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
        $fecha_actual = date('Y-m-d');
        $select = new Select();
        $select->from('BNF3_Campanias');
        $select->where->literal("IFNULL( Timestampdiff(day,'" . $fecha_actual . "',VigenciaFin) ,1) < 1")
            ->and->in('EstadoCampania', array('Publicado', 'Borrador'));
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getEmpresasProveedora()
    {
        $select = new Select();
        $select->from('BNF3_Campanias');
        $select->columns(array());
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Segmentos.id = BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id',
            array()
        );
        $select->join(
            'BNF3_Oferta_Premios',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Oferta_Premios.BNF_Empresa_id = BNF_Empresa.id',
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
        $select->from('BNF3_Campanias');
        $select->columns(array('id', 'NombreCampania'));
        $select->join(
            'BNF3_Segmentos',
            'BNF3_Campanias.id = BNF3_Segmentos.BNF3_Campania_id',
            array()
        );
        $select->join(
            'BNF3_Oferta_Premios_Segmentos',
            'BNF3_Segmentos.id = BNF3_Oferta_Premios_Segmentos.BNF3_Segmento_id',
            array()
        );
        $select->join(
            'BNF3_Oferta_Premios',
            'BNF3_Oferta_Premios.id = BNF3_Oferta_Premios_Segmentos.BNF3_Oferta_Premios_id',
            array()
        );
        $select->join(
            'BNF_Empresa',
            'BNF3_Oferta_Premios.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        $select->where->equalTo("BNF_Empresa.id", $empresa)
            ->AND->equalTo("BNF3_Campanias.Eliminado", 0)
            ->AND->equalTo("BNF3_Segmentos.Eliminado", 0);

        $select->group('BNF3_Campanias.id');
        //echo $select->getSqlString();exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
