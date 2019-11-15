<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 15/09/15
 * Time: 02:46 PM
 */

namespace Oferta\Model\Table;

use Oferta\Model\Oferta;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\Select;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class OfertaTable
{
    protected $tableGateway;
    const TIPO_OFERTA_PRESENCIA = 2;
    const ESTADO_OFERTA_ELIMINADA = '0';
    const ESTADO_CAMPANIAS_ELIMINADA = 0;
    const ESTADO_CAMPANIASUBIGEO_ELIMINADA = '0';
    const ESTADO_OFERTACAMPANIAUBIGEO_ELIMINADA = '0';
    const ESTADO_CATEGORIA_ELIMINADA = 0;
    const ESTADO_CATEGORIASUBIGEO_ELIMINADA = '0';
    const ESTADO_OFERTACATEGORIAUBIGEO_ELIMINADA = '0';
    const ESTADO_RUBRO_ELIMINADA = 0;
    const ESTADO_OFERTARUBRO_ELIMINADA = '0';
    const ESTADO_TIPO_EMPRESA_ELIMINADA = 0;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select(array('Eliminado' => $this::ESTADO_OFERTA_ELIMINADA));
        return $resultSet;
    }

    public function getOferta($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->where->equalTo("BNF_Oferta.id", $id);
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->current();
    }

    public function getOfertaExitsXTipoPaqueteEmpresa($tipo, $empresa)
    {
        $tipo = (int)$tipo;
        $empresa = (int)$empresa;
        $rowset = $this->tableGateway->select(
            array('BNF_BolsaTotal_TipoPaquete_id' => $tipo, 'BNF_BolsaTotal_Empresa_id' => $empresa)
        );
        $row = $rowset->current();
        if (!$row) {
            return true;
        }
        return false;
    }

    public function getOfertaExits($id)
    {
        $id = (int)$id;
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->where("id = " . $id);
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertaDetails(
        $emp = null,
        $tipo = null,
        $rubro = null,
        $cat = null,
        $cam = null,
        $nom = null,
        $order_by = "",
        $order = ""
    )
    {
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->columns(
            array('id', 'BNF_BolsaTotal_TipoPaquete_id', 'Titulo', 'DatoBeneficio', 'Eliminado')
        );
        $select->join(
            'BNF_BolsaTotal',
            'BNF_Oferta.BNF_BolsaTotal_TipoPaquete_id = BNF_BolsaTotal.BNF_TipoPaquete_id ' .
            'AND BNF_Oferta.BNF_BolsaTotal_Empresa_id = BNF_BolsaTotal.BNF_Empresa_id'
        );
        $select->join(
            'BNF_TipoPaquete',
            'BNF_TipoPaquete.id = BNF_BolsaTotal.BNF_TipoPaquete_id',
            array('TipoOferta' => 'NombreTipoPaquete')
        );
        $select->join(
            'BNF_OfertaCategoriaUbigeo',
            'BNF_Oferta.id = BNF_OfertaCategoriaUbigeo.BNF_Oferta_id',
            array()
        );
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF_OfertaCategoriaUbigeo.BNF_CategoriaUbigeo_id = BNF_CategoriaUbigeo.id',
            array()
        );
        $select->join(
            'BNF_Categoria',
            'BNF_CategoriaUbigeo.BNF_Categoria_id = BNF_Categoria.id',
            array()
        );
        if ($cam == null) {
            $select->join(
                'BNF_OfertaCampaniaUbigeo',
                'BNF_Oferta.id = BNF_OfertaCampaniaUbigeo.BNF_Oferta_id',
                array(),
                "left"
            );
            $select->join(
                'BNF_CampaniaUbigeo',
                'BNF_OfertaCampaniaUbigeo.BNF_CampaniaUbigeo_id = BNF_CampaniaUbigeo.id',
                array(),
                "left"
            );
            $select->join(
                'BNF_Campanias',
                'BNF_CampaniaUbigeo.BNF_Campanias_id = BNF_Campanias.id',
                array(),
                "left"
            );
        } else {
            $select->join(
                'BNF_OfertaCampaniaUbigeo',
                'BNF_Oferta.id = BNF_OfertaCampaniaUbigeo.BNF_Oferta_id',
                array()
            );
            $select->join(
                'BNF_CampaniaUbigeo',
                'BNF_OfertaCampaniaUbigeo.BNF_CampaniaUbigeo_id = BNF_CampaniaUbigeo.id',
                array()
            );
            $select->join(
                'BNF_Campanias',
                'BNF_CampaniaUbigeo.BNF_Campanias_id = BNF_Campanias.id',
                array()
            );
        }

        $select->join(
            'BNF_OfertaRubro',
            'BNF_OfertaRubro.BNF_Oferta_id = BNF_Oferta.id',
            array()
        );
        $select->join(
            'BNF_Rubro',
            'BNF_Rubro.id = BNF_OfertaRubro.BNF_Rubro_id',
            array()
        );
        $select->join(
            'BNF_OfertaEmpresaCliente',
            'BNF_Oferta.id = BNF_OfertaEmpresaCliente.BNF_Oferta_id',
            array(
                "Asignaciones" => new Expression(
                    "(SELECT COUNT(*) FROM BNF_OfertaEmpresaCliente " .
                    "WHERE BNF_Oferta.id = BNF_OfertaEmpresaCliente.BNF_Oferta_id AND Eliminado = '0')"
                )
            ),
            'Left'
        );
        $select->join(
            'BNF_Empresa',
            'BNF_Oferta.BNF_BolsaTotal_Empresa_id = BNF_Empresa.id',
            array('Nombre' => 'NombreComercial')
        );
        $select->join(
            'BNF_EmpresaTipoEmpresa',
            'BNF_EmpresaTipoEmpresa.BNF_Empresa_id = BNF_Empresa.id',
            array()
        );

        if ($cam != null) {
            $select->where->equalTo('BNF_Campanias.id', $cam);
            $select->where->equalTo('BNF_Campanias.Eliminado', $this::ESTADO_CAMPANIAS_ELIMINADA);
            $select->where->equalTo('BNF_CampaniaUbigeo.Eliminado', $this::ESTADO_CAMPANIASUBIGEO_ELIMINADA);
            $select->where->equalTo(
                'BNF_OfertaCampaniaUbigeo.Eliminado',
                $this::ESTADO_OFERTACAMPANIAUBIGEO_ELIMINADA
            );
        }

        if ($cat != null) {
            $select->where->equalTo('BNF_Categoria.id', $cat);
            $select->where->equalTo('BNF_Categoria.Eliminado', $this::ESTADO_CATEGORIA_ELIMINADA);
            $select->where->equalTo('BNF_CategoriaUbigeo.Eliminado', $this::ESTADO_CATEGORIASUBIGEO_ELIMINADA);
            $select->where->equalTo(
                'BNF_OfertaCategoriaUbigeo.Eliminado',
                $this::ESTADO_OFERTACATEGORIAUBIGEO_ELIMINADA
            );
        }

        if ($rubro != null) {
            $select->where->equalTo('BNF_Rubro.id', $rubro);
            $select->where->equalTo('BNF_Rubro.Eliminado', $this::ESTADO_RUBRO_ELIMINADA);
            $select->where->equalTo('BNF_OfertaRubro.Eliminado', $this::ESTADO_OFERTARUBRO_ELIMINADA);
        }

        if ($tipo != null) {
            $select->where->equalTo('BNF_BolsaTotal_TipoPaquete_id', $tipo);
        }

        if ($emp != null) {
            $select->where->equalTo('BNF_BolsaTotal_Empresa_id', $emp);
            $select->where->equalTo('BNF_EmpresaTipoEmpresa.Eliminado', $this::ESTADO_TIPO_EMPRESA_ELIMINADA);
        }

        if ($nom != null) {
            $select->where->equalTo('BNF_Oferta.Titulo', $nom);
            $select->where->equalTo('BNF_Oferta.Eliminado', $this::ESTADO_OFERTA_ELIMINADA);
        }

        if (isset($order_by) && $order_by != "" && $order_by != 'id') {
            $select->order($order_by . ' ' . $order);
        } else {
            $select->order("BNF_Oferta.FechaCreacion DESC");
        }

        $select->group('BNF_Oferta.id');

        //echo str_replace('"','', $select->getSqlString());exit;

        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Oferta());
        $paginatorAdapter = new DbSelect(
            $select,
            $this->tableGateway->getAdapter(),
            $resultSetPrototype
        );
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }

    public function saveOferta(Oferta $oferta)
    {
        $data = array(
            'BNF_TipoBeneficio_id' => $oferta->BNF_TipoBeneficio_id,
            'Nombre' => $oferta->Nombre,
            'Titulo' => $oferta->Titulo,
            'TituloCorto' => $oferta->TituloCorto,
            'SubTitulo' => $oferta->SubTitulo,
            'FormatoBeneficio' => $oferta->FormatoBeneficio,
            'DatoBeneficio' => (is_numeric($oferta->DatoBeneficio))
                ? number_format($oferta->DatoBeneficio) : $oferta->DatoBeneficio,
            'Descripcion' => $oferta->Descripcion,
            'CondicionesUso' => $oferta->CondicionesUso,
            'Direccion' => $oferta->Direccion,
            'Telefono' => $oferta->Telefono,
            'Premium' => (int)$oferta->Premium,
            'Distrito' => $oferta->Distrito,
            'FechaFinVigencia' => $oferta->FechaFinVigencia,
            'FechaInicioPublicacion' => $oferta->FechaInicioPublicacion,
            'FechaFinPublicacion' => $oferta->FechaFinPublicacion,
            'Stock' => $oferta->Stock,
            'StockInicial' => $oferta->StockInicial,
            'Correo' => $oferta->Correo,
            'Estado' => $oferta->Estado,
            'DescargaMaximaDia' => $oferta->DescargaMaximaDia,
            'Eliminado' => (int)$oferta->Eliminado,
            'BNF_BolsaTotal_TipoPaquete_id' => $oferta->BNF_BolsaTotal_TipoPaquete_id,
            'BNF_BolsaTotal_Empresa_id' => $oferta->BNF_BolsaTotal_Empresa_id,
            'Slug' => $oferta->Slug,
            'CondicionesTebca' => $oferta->CondicionesTebca,
            'TipoAtributo' => $oferta->TipoAtributo,
        );

        $id = (int)$oferta->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        } else {
            if ($this->getOferta($id)) {
                $data['FechaActualizacion'] = date("Y-m-d H:i:s");
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('La Oferta no existe');
            }
        }
        return $id;
    }

    public function deleteOferta($id, $val)
    {
        $data['Eliminado'] = $val;
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function updateOferta($id, $data)
    {
        $this->tableGateway->update($data, array('id' => (int)$id));
    }

    public function getReports($empresa_id)
    {
        $select = new Select();
        $select->from(array('of' => 'BNF_Oferta'));
        $select->join(
            'BNF_BolsaTotal',
            'of.BNF_BolsaTotal_TipoPaquete_id = BNF_BolsaTotal.BNF_TipoPaquete_id ' .
            'AND of.BNF_BolsaTotal_Empresa_id = BNF_BolsaTotal.BNF_Empresa_id'
        );
        $select->join(
            'BNF_TipoPaquete',
            'BNF_TipoPaquete.id = BNF_BolsaTotal.BNF_TipoPaquete_id',
            array('TipoOferta' => 'NombreTipoPaquete')
        );
        $select->join(
            'BNF_TipoBeneficio',
            'of.BNF_TipoBeneficio_id = BNF_TipoBeneficio.id',
            array('BNF_TipoBeneficio_id' => 'NombreBeneficio')
        );
        $select->join('BNF_OfertaCategoriaUbigeo', 'of.id = BNF_OfertaCategoriaUbigeo.BNF_Oferta_id', array());
        $select->join(
            'BNF_CategoriaUbigeo',
            'BNF_OfertaCategoriaUbigeo.BNF_CategoriaUbigeo_id = BNF_CategoriaUbigeo.id',
            array()
        );
        $select->join('BNF_Categoria', 'BNF_CategoriaUbigeo.BNF_Categoria_id = BNF_Categoria.id', array());
        $select->join('BNF_OfertaCampaniaUbigeo', 'of.id = BNF_OfertaCampaniaUbigeo.BNF_Oferta_id', array(), "left");
        $select->join(
            'BNF_CampaniaUbigeo',
            'BNF_OfertaCampaniaUbigeo.BNF_CampaniaUbigeo_id = BNF_CampaniaUbigeo.id',
            array(),
            "left"
        );
        $select->join('BNF_Campanias', 'BNF_CampaniaUbigeo.BNF_Campanias_id = BNF_Campanias.id', array(), "left");
        $select->join('BNF_OfertaRubro', 'BNF_OfertaRubro.BNF_Oferta_id = of.id', array());
        $select->join('BNF_Rubro', 'BNF_Rubro.id = BNF_OfertaRubro.BNF_Rubro_id', array());
        if ((int)$empresa_id != 0) {
            $select->where->equalTo('BNF_Empresa_id', $empresa_id);
        }
        $select->group('of.id');

        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }

    public function getOfertasCaducadas($tipo)
    {
        $fecha_actual = date('Y-m-d');
        $idtipo = (int)$tipo;
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->columns(
            array(
                'id',
                'FechaFinVigencia' => new Expression('(IFNULL(BNF_Oferta.FechaFinVigencia, BNF_Oferta_Atributos.FechaVigencia))'),
                'Stock' => new Expression('(IFNULL(BNF_Oferta.Stock, BNF_Oferta_Atributos.Stock))'),
                'TipoAtributo',
                'BNF_BolsaTotal_TipoPaquete_id',
                'BNF_BolsaTotal_Empresa_id'
            )
        );

        $select->join(
            "BNF_Oferta_Atributos",
            "BNF_Oferta_Atributos.BNF_Oferta_id = BNF_Oferta.id",
            array('Atributo_id' => 'id'),
            'left'
        );

        $select->where->literal(
            "IFNULL(TIMESTAMPDIFF(DAY,
                '" . $fecha_actual . "',
                (IFNULL(BNF_Oferta.FechaFinVigencia,
                        BNF_Oferta_Atributos.FechaVigencia))),
            1) < 1"
        )->and->equalTo('BNF_BolsaTotal_TipoPaquete_id', $idtipo)
            ->and->equalTo('Estado', 'Caducado')
            ->and->equalTo('BNF_Oferta.Eliminado', $this::ESTADO_OFERTA_ELIMINADA);
        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertasFinalizadas($tipo)
    {
        $fecha_actual = date('Y-m-d');
        $idtipo = (int)$tipo;
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->columns(
            array(
                'id',
                'Stock' => new Expression('(IFNULL(BNF_Oferta.Stock, BNF_Oferta_Atributos.Stock))'),
                'TipoAtributo',
                'BNF_BolsaTotal_TipoPaquete_id',
                'BNF_BolsaTotal_Empresa_id'
            )
        );

        $select->join(
            "BNF_Oferta_Atributos",
            "BNF_Oferta_Atributos.BNF_Oferta_id = BNF_Oferta.id",
            array('Atributo_id' => 'id'),
            'left'
        );

        $select->where->literal(
            "IFNULL(TIMESTAMPDIFF(DAY,
                '" . $fecha_actual . "',
                FechaFinPublicacion),
            1) < 1"
        )->and->equalTo('BNF_BolsaTotal_TipoPaquete_id', $idtipo)
            ->and->equalTo('Estado', 'Publicado')
            ->and->equalTo('BNF_Oferta.Eliminado', $this::ESTADO_OFERTA_ELIMINADA);
        //echo str_replace('"', '', $select->getSqlString()) . "\n";exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function updateOfertasFinalizadas($id)
    {
        $data['Stock'] = 0;
        $data['StockInicial'] = 0;
        $data['Estado'] = "Caducado";
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        return $this->tableGateway->update($data, array('id' => $id));
    }

    public function updateOfertasVencidas($id)
    {
        $data['Estado'] = "Caducado";
        $data['FechaActualizacion'] = date("Y-m-d H:i:s");
        return $this->tableGateway->update($data, array('id' => $id));
    }

    public function getOfertasTitulo($tipo = null, $empresa = null)
    {
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->columns(array('id', 'Titulo'));
        $select->order("FechaCreacion DESC");
        if ($tipo != null) {
            $select->where->equalTo('BNF_BolsaTotal_TipoPaquete_id', $tipo)
                ->and->equalTo('Estado', 'Publicado');
        }

        if ($empresa != null) {
            $select->where->equalTo('BNF_BolsaTotal_Empresa_id', $empresa);
        }

        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertasConsumidas(
        $fechainicio = null,
        $fechafin = null,
        $titulo = '',
        $estado = '',
        $empresa_id = 0
    )
    {
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->columns(
            array(
                'id',
                'BNF_BolsaTotal_TipoPaquete_id',
                'Titulo',
                'FechaInicioPublicacion' => new Expression("DATE_FORMAT(FechaInicioPublicacion,'%Y-%m-%d')"),
                'Estado',
                'Stock',
                'Descargados' => new Expression(
                    'IF(BNF_BolsaTotal_TipoPaquete_id <> 3,' .
                    '(SELECT count(*) FROM  BNF_Cupon WHERE BNF_Oferta_id = BNF_Oferta.id ' .
                    'AND FechaGenerado IS NOT NULL),NULL)'
                ),
                'NoUtilizados' => new Expression(
                    'IF(BNF_BolsaTotal_TipoPaquete_id <> 3,' .
                    'Stock - (SELECT count(*) FROM BNF_Cupon WHERE BNF_Oferta_id = BNF_Oferta.id ' .
                    'AND FechaGenerado IS NOT NULL),NULL)'
                ),
                'Redimidos' => new Expression(
                    "IF(BNF_BolsaTotal_TipoPaquete_id <> 3," .
                    "(SELECT count(*) FROM  BNF_Cupon WHERE BNF_Oferta_id = BNF_Oferta.id " .
                    " AND BNF_Cupon.EstadoCupon = 'Redimido'),NULL)"
                ),
            )
        );

        if ((int)$empresa_id != 0) {
            $select->where->equalTo('BNF_Oferta.BNF_BolsaTotal_Empresa_id', $empresa_id);
        }

        if ($titulo == null and $estado != null) {
            $select->where->equalTo("Estado", $estado);
        } elseif ($titulo != null and $estado == null) {
            $select->where->equalTo("id", $titulo);
        } elseif ($titulo != null and $estado != null) {
            $select->where->literal("(id = " . $titulo . " OR Estado = '" . $estado . "' )");
        }

        if (!empty($fechainicio) or !empty($fechafin)) {
            $select->where->and->between('FechaInicioPublicacion', $fechainicio, $fechafin);
        }

        $select->order('FechaCreacion DESC');
        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->buffer();
    }

    public function getOfertasPresencia()
    {
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->columns(
            array(
                '*',
                'Stock' => new Expression('(IFNULL(BNF_Oferta.Stock, BNF_Oferta_Atributos.Stock))'),
                'StockInicial' => new Expression('(IFNULL(BNF_Oferta.StockInicial, BNF_Oferta_Atributos.StockInicial))'),
                'FechaFinVigencia' => new Expression('(IFNULL(BNF_Oferta.FechaFinVigencia, BNF_Oferta_Atributos.FechaVigencia))'),
            )
        );

        $select->join(
            "BNF_Oferta_Atributos",
            "BNF_Oferta_Atributos.BNF_Oferta_id = BNF_Oferta.id",
            array('Atributo_id' => 'id'),
            'left'
        );

        $select->where->equalTo('BNF_BolsaTotal_TipoPaquete_id', $this::TIPO_OFERTA_PRESENCIA)
            ->and->equalTo('BNF_Oferta.Eliminado', $this::ESTADO_OFERTA_ELIMINADA)
            ->and->equalTo('Estado', 'Publicado');

        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getTotalOfertasCaducadas($fechaini = "", $fechafin = "")
    {
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->where->equalTo('Estado', 'Caducado');
        if ($fechaini != null || $fechafin != null) {
            if ($fechaini == null) {
                $fechaini = '1900-01-01';
            }
            if ($fechafin == null) {
                $fechafin = date("Y-m-d");
            }
            $select->where(
                "FechaCreacion BETWEEN '$fechaini' AND ADDDATE('$fechafin', INTERVAL 1 DAY)"
            );
        }
        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getTotalOfertasPublicadas($fechaini = "", $fechafin = "")
    {
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->where->equalTo('Estado', 'Publicado');
        if ($fechaini != null || $fechafin != null) {
            if ($fechaini == null) {
                $fechaini = '1900-01-01';
            }
            if ($fechafin == null) {
                $fechafin = date("Y-m-d");
            }
            $select->where(
                "FechaCreacion BETWEEN '$fechaini' AND ADDDATE('$fechafin', INTERVAL 1 DAY)"
            );
        }
        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getTotalOfertasPendientes($fechaini = "", $fechafin = "")
    {
        $select = new Select();
        $select->from('BNF_Oferta');
        $select->where->equalTo('Estado', 'Pendiente');
        if ($fechaini != null || $fechafin != null) {
            if ($fechaini == null) {
                $fechaini = '1900-01-01';
            }
            if ($fechafin == null) {
                $fechafin = date("Y-m-d");
            }
            $select->where(
                "FechaCreacion BETWEEN '$fechaini' AND ADDDATE('$fechafin', INTERVAL 1 DAY)"
            );
        }
        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getOfertasPublicadas()
    {
        $select = new Select();
        $select->from(array('o' => 'BNF_Oferta'));
        $select->columns(array('TituloCorto', 'FechaFinPublicacion', 'Stock'));
        $select->join(array('e' => 'BNF_Empresa'), 'e.id = o.BNF_BolsaTotal_Empresa_id', array('NombreComercial'));
        $select->join(array('ocu' => 'BNF_OfertaCategoriaUbigeo'), 'o.id = ocu.BNF_Oferta_id', array());
        $select->join(array('cu' => 'BNF_CategoriaUbigeo'), 'ocu.BNF_CategoriaUbigeo_id = cu.id', array());
        $select->join(array('c' => 'BNF_Categoria'), 'cu.BNF_Categoria_id = c.id', array('Categoria' => 'Nombre'));
        $select->where->equalTo('o.Estado', 'Publicado');

        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertarxCategoria($id, $empresa_id)
    {
        $select = new Select();
        $select->from(array('o' => 'BNF_Oferta'));
        $select->join(array('ocu' => 'BNF_OfertaCategoriaUbigeo'), 'o.id = ocu.BNF_Oferta_id', array());
        $select->join(array('cu' => 'BNF_CategoriaUbigeo'), 'ocu.BNF_CategoriaUbigeo_id = cu.id', array());
        $select->join(array('c' => 'BNF_Categoria'), 'cu.BNF_Categoria_id = c.id', array());
        if ($empresa_id > 0) {
            $select->join(array('oec' => 'BNF_OfertaEmpresaCliente'), 'o.id = oec.BNF_Oferta_id', array());
        }
        $select->where->equalTo('o.Estado', 'Publicado')
            ->and->equalTo('c.id', $id);
        if ($empresa_id > 0) {
            $select->where->equalTo('oec.BNF_Empresa_id', $empresa_id)
                ->and->equalTo('oec.Eliminado', '0');
        }

        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertarxCampania($id, $empresa_id)
    {
        $select = new Select();
        $select->from(array('o' => 'BNF_Oferta'));
        $select->join(array('ocu' => 'BNF_OfertaCampaniaUbigeo'), 'o.id = ocu.BNF_Oferta_id', array());
        $select->join(array('cu' => 'BNF_CampaniaUbigeo'), 'ocu.BNF_CampaniaUbigeo_id = cu.id', array());
        $select->join(array('c' => 'BNF_Campanias'), 'cu.BNF_Campanias_id = c.id', array());
        if ($empresa_id > 0) {
            $select->join(array('oec' => 'BNF_OfertaEmpresaCliente'), 'o.id = oec.BNF_Oferta_id', array());
        }
        $select->where->equalTo('o.Estado', 'Publicado')
            ->and->equalTo('c.id', $id);
        if ($empresa_id > 0) {
            $select->where->equalTo('oec.BNF_Empresa_id', $empresa_id)
                ->and->equalTo('oec.Eliminado', '0');
        }

        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getOfertaEmpresaCliente($empresa_id)
    {
        $select = new Select();
        $select->from(array('o' => 'BNF_Oferta'));
        if ($empresa_id > 0) {
            $select->join(array('oec' => 'BNF_OfertaEmpresaCliente'), 'o.id = oec.BNF_Oferta_id', array());
        }
        $select->where->equalTo('o.Estado', 'Publicado');
        if ($empresa_id > 0) {
            $select->where->equalTo('oec.BNF_Empresa_id', $empresa_id)
                ->and->equalTo('oec.Eliminado', '0');
        }

        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }

    public function getCantDescargasPorOferta($fechaini, $fechafin)
    {
        $select = new Select();
        $select->from(array('O' => 'BNF_Oferta'));
        $select->columns(
            array(
                'Titulo',
                'DatoBeneficio',
                'Descargas' => new Expression(
                    "(SELECT COUNT(*) FROM BNF_Cupon WHERE FechaGenerado IS NOT NULL AND BNF_Oferta_id = O.id " .
                    "AND FechaGenerado BETWEEN '" . $fechaini . "' AND ADDDATE('" . $fechafin . "', INTERVAL 1 DAY))"
                )
            )
        );
        $select->join('BNF_Empresa', 'O.BNF_BolsaTotal_Empresa_id=BNF_Empresa.id', array('NombreComercial'));
        $select->where->equalTo('Estado', 'Publicado');
        $select->order('Descargas DESC');

        //echo str_replace('"','', $select->getSqlString());exit;
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
