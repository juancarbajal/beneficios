<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/01/16
 * Time: 07:32 PM
 */

namespace Application\Model\Table;

use Application\Model\OfertaFormClienteLead;
use Zend\Db\TableGateway\TableGateway;

class OfertaFormClienteLeadTable
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function saveFormulario(OfertaFormClienteLead $formlead)
    {
        $data = array(
            'BNF_Oferta_id' => $formlead->BNF_Oferta_id,
            'BNF_Cliente_id' => $formlead->BNF_Cliente_id,
            'BNF_Empresa_id' => $formlead->BNF_Empresa_id,
            'BNF_Categoria_id' => $formlead->BNF_Categoria_id,
            'BNF_Formulario_id' => $formlead->BNF_Formulario_id,
            'Descripcion' => $formlead->Descripcion,
        );
        $id = (int)$formlead->id;
        if ($id == 0) {
            $data['FechaCreacion'] = date("Y-m-d H:i:s");
            $this->tableGateway->insert($data);
            $id = $this->tableGateway->getLastInsertValue();
        }
        return $id;
    }
}
