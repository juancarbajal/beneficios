<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 27/06/16
 * Time: 06:37 PM
 */

namespace Demanda\Model;

class DemandaLog
{
    public $id;
    public $BNF2_Demanda_id;
    public $BNF_Empresa_id;
    public $FechaDemanda;
    public $PrecioMinimo;
    public $PrecioMaximo;
    public $Target;
    public $Comentarios;
    public $Actualizaciones;
    public $Eliminado;
    public $Rubros;
    public $Segmentos;
    public $EmpresaProveedor;
    public $EmpresasAdicionales;
    public $Departamentos;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF2_Demanda_id = (!empty($data['BNF2_Demanda_id'])) ? $data['BNF2_Demanda_id'] : null;
        $this->BNF_Empresa_id = (!empty($data['BNF_Empresa_id'])) ? $data['BNF_Empresa_id'] : null;
        $this->FechaDemanda = (!empty($data['FechaDemanda'])) ? $data['FechaDemanda'] : null;
        $this->PrecioMinimo = (!empty($data['PrecioMinimo'])) ? $data['PrecioMinimo'] : null;
        $this->PrecioMaximo = (!empty($data['PrecioMaximo'])) ? $data['PrecioMaximo'] : null;
        $this->Target = (!empty($data['Target'])) ? $data['Target'] : null;
        $this->Comentarios = (!empty($data['Comentarios'])) ? $data['Comentarios'] : null;
        $this->Actualizaciones = (!empty($data['Actualizaciones'])) ? $data['Actualizaciones'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->Rubros = (!empty($data['Rubros'])) ? $data['Rubros'] : null;
        $this->Segmentos = (!empty($data['Segmentos'])) ? $data['Segmentos'] : null;
        $this->EmpresaProveedor = (!empty($data['EmpresaProveedor'])) ? $data['EmpresaProveedor'] : null;
        $this->EmpresasAdicionales = (!empty($data['EmpresasAdicionales'])) ? $data['EmpresasAdicionales'] : null;
        $this->Departamentos = (!empty($data['Departamentos'])) ? $data['Departamentos'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
