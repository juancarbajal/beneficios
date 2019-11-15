<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 08/01/16
 * Time: 07:56 PM
 */

namespace Reportes\Model\Table;

use Reportes\Model\OfertaFormCliente;
use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;

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

    public function getCount($empresa_id, $fechaInicio, $fechaFin, $categoria = null, $id_cliente = null)
    {
        $id = (int)$empresa_id;

        $select = new Select();
        $select->from('BNF_OfertaFormCliente');
        if ($categoria != null) {
            $select->where
                ->equalTo('BNF_OfertaFormCliente.BNF_Categoria_id', $categoria);
        }
        if ($id_cliente != null) {
            $select->where
                ->equalTo('BNF_OfertaFormCliente.BNF_Cliente_id', $id_cliente);
        }
        if ($empresa_id != '') {
            $select->where->equalTo('BNF_OfertaFormCliente.BNF_Empresa_id', $id);
        }
        $select->where(
            "BNF_OfertaFormCliente.FechaCreacion BETWEEN '$fechaInicio' AND ADDDATE('$fechaFin', INTERVAL 1 DAY)"
        );
        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet->count();
    }

    public function getTotalEnviosPorEmpresa($fechaini = "", $fechafin = "")
    {
        $select = new Select();
        $select->from('BNF_OfertaFormCliente');
        $select->columns(
            array(
                'Total' => new Expression('COUNT(*)')
            )
        );
        $select->join(
            'BNF_Oferta',
            'BNF_OfertaFormCliente.BNF_Oferta_id = BNF_Oferta.id',
            array('Empresa' => 'BNF_BolsaTotal_Empresa_id')
        );

        $select->where->equalTo('BNF_BolsaTotal_TipoPaquete_id', 3);

        if ($fechaini != null || $fechafin != null) {
            if ($fechaini == null) {
                $fechaini = '1900-01-01';
            }
            if ($fechafin == null) {
                $fechafin = date("Y-m-d");
            }
            $select->where(
                "BNF_OfertaFormCliente.FechaCreacion BETWEEN '$fechaini' AND ADDDATE('$fechafin', INTERVAL 1 DAY)"
            );
        }

        $select->group('BNF_BolsaTotal_Empresa_id');
        /*$query = $select->getSqlString();
        echo str_replace('"', '', $query);
        exit;*/
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
}
