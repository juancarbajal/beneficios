<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 12/01/16
 * Time: 10:44 AM
 */

namespace Reportes\Model;

class DmMetCliente
{
    public $id;
    public $BNF_DM_Dim_EstadoCivil_id;
    public $BNF_DM_DIM_Localidad_id;
    public $BNF_DM_Dim_Empresa_id;
    public $BNF_DM_Dim_Hijos_id;
    public $BNF_DM_Dim_Edad_id;
    public $BNF_Cliente_id;
    public $BNF_Cliente_FechaCreacion;
    public $DiasUltimoLogin;
    public $Edad;
    public $Genero;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_DM_Dim_EstadoCivil_id = (!empty($data['BNF_DM_Dim_EstadoCivil_id']))
            ? $data['BNF_DM_Dim_EstadoCivil_id'] : null;
        $this->BNF_DM_DIM_Localidad_id = (!empty($data['BNF_DM_DIM_Localidad_id']))
            ? $data['BNF_DM_DIM_Localidad_id'] : null;
        $this->BNF_DM_Dim_Empresa_id = (!empty($data['BNF_DM_Dim_Empresa_id'])) ? $data['BNF_DM_Dim_Empresa_id'] : null;
        $this->BNF_DM_Dim_Hijos_id = (!empty($data['BNF_DM_Dim_Hijos_id'])) ? $data['BNF_DM_Dim_Hijos_id'] : null;
        $this->BNF_DM_Dim_Edad_id = (!empty($data['BNF_DM_Dim_Edad_id'])) ? $data['BNF_DM_Dim_Edad_id'] : null;
        $this->BNF_Cliente_id = (!empty($data['BNF_Cliente_id'])) ? $data['BNF_Cliente_id'] : null;
        $this->BNF_Cliente_FechaCreacion = (!empty($data['BNF_Cliente_FechaCreacion']))
            ? $data['BNF_Cliente_FechaCreacion'] : null;
        $this->DiasUltimoLogin = (!empty($data['DiasUltimoLogin'])) ? $data['DiasUltimoLogin'] : null;
        $this->Edad = (!empty($data['Edad'])) ? $data['Edad'] : null;
        $this->Genero = (!empty($data['Genero'])) ? $data['Genero'] : null;

        $this->localidad = (!empty($data['localidad'])) ? $data['localidad'] : null;
        $this->Cantidad = (!empty($data['Cantidad'])) ? $data['Cantidad'] : null;

        $this->estado = (!empty($data['estado'])) ? $data['estado'] : null;
        $this->hijos = (!empty($data['estado'])) ? $data['estado'] : null;

        $this->Descargas = (!empty($data['Descargas'])) ? $data['Descargas'] : null;
        $this->DesCat1 = (!empty($data['DesCat1'])) ? $data['DesCat1'] : null;
        $this->DesCat2 = (!empty($data['DesCat2'])) ? $data['DesCat2'] : null;
        $this->DesCat3 = (!empty($data['DesCat3'])) ? $data['DesCat3'] : null;
        $this->DesCat4 = (!empty($data['DesCat4'])) ? $data['DesCat4'] : null;
        $this->DesCat5 = (!empty($data['DesCat5'])) ? $data['DesCat5'] : null;
        $this->DesCat6 = (!empty($data['DesCat6'])) ? $data['DesCat6'] : null;
        $this->DesCat7 = (!empty($data['DesCat7'])) ? $data['DesCat7'] : null;
        $this->DesCat8 = (!empty($data['DesCat8'])) ? $data['DesCat8'] : null;
        $this->DesCat9 = (!empty($data['DesCat9'])) ? $data['DesCat9'] : null;
        $this->DesCat10 = (!empty($data['DesCat10'])) ? $data['DesCat10'] : null;
        $this->DesCat11 = (!empty($data['DesCat11'])) ? $data['DesCat11'] : null;
        $this->DesCat12 = (!empty($data['DesCat12'])) ? $data['DesCat12'] : null;
        $this->DesCat13 = (!empty($data['DesCat13'])) ? $data['DesCat13'] : null;
        $this->DesCat14 = (!empty($data['DesCat14'])) ? $data['DesCat14'] : null;
        $this->DesCat15 = (!empty($data['DesCat15'])) ? $data['DesCat15'] : null;
        $this->DesCatBus = (!empty($data['DesCatBus'])) ? $data['DesCatBus'] : null;
        $this->DesCatCom = (!empty($data['DesCatCom'])) ? $data['DesCatCom'] : null;
        $this->DesCatCam = (!empty($data['DesCatCam'])) ? $data['DesCatCam'] : null;
        $this->DesCatTie = (!empty($data['DesCatTie'])) ? $data['DesCatTie'] : null;


        $this->Redimidos = (!empty($data['Redimidos'])) ? $data['Redimidos'] : null;
        $this->RedCat1 = (!empty($data['RedCat1'])) ? $data['RedCat1'] : null;
        $this->RedCat2 = (!empty($data['RedCat2'])) ? $data['RedCat2'] : null;
        $this->RedCat3 = (!empty($data['RedCat3'])) ? $data['RedCat3'] : null;
        $this->RedCat4 = (!empty($data['RedCat4'])) ? $data['RedCat4'] : null;
        $this->RedCat5 = (!empty($data['RedCat5'])) ? $data['RedCat5'] : null;
        $this->RedCat6 = (!empty($data['RedCat6'])) ? $data['RedCat6'] : null;
        $this->RedCat7 = (!empty($data['RedCat7'])) ? $data['RedCat7'] : null;
        $this->RedCat8 = (!empty($data['RedCat8'])) ? $data['RedCat8'] : null;
        $this->RedCat9 = (!empty($data['RedCat9'])) ? $data['RedCat9'] : null;
        $this->RedCat10 = (!empty($data['RedCat10'])) ? $data['RedCat10'] : null;
        $this->RedCat11 = (!empty($data['RedCat11'])) ? $data['RedCat11'] : null;
        $this->RedCat12 = (!empty($data['RedCat12'])) ? $data['RedCat12'] : null;
        $this->RedCat13 = (!empty($data['RedCat13'])) ? $data['RedCat13'] : null;
        $this->RedCat14 = (!empty($data['RedCat14'])) ? $data['RedCat14'] : null;
        $this->RedCat15 = (!empty($data['RedCat15'])) ? $data['RedCat15'] : null;
        $this->RedCatBus = (!empty($data['RedCatBus'])) ? $data['RedCatBus'] : null;
        $this->RedCatCom = (!empty($data['RedCatCom'])) ? $data['RedCatCom'] : null;
        $this->RedCatCam = (!empty($data['RedCatCam'])) ? $data['RedCatCam'] : null;
        $this->RedCatTie = (!empty($data['RedCatTie'])) ? $data['RedCatTie'] : null;


        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->NumeroDocumento = (!empty($data['NumeroDocumento'])) ? $data['NumeroDocumento'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Apellido = (!empty($data['Apellido'])) ? $data['Apellido'] : null;
        $this->distrito_trabaja = (!empty($data['distrito_trabaja'])) ? $data['distrito_trabaja'] : null;
        $this->distrito_vive = (!empty($data['distrito_vive'])) ? $data['distrito_vive'] : null;
        $this->NoDef = (!empty($data['NoDef'])) ? $data['NoDef'] : null;
        $this->NoHijos = (!empty($data['NoHijos'])) ? $data['NoHijos'] : null;
        $this->SiHijos = (!empty($data['SiHijos'])) ? $data['SiHijos'] : null;
        $this->Correo = (!empty($data['Correo'])) ? $data['Correo'] : null;
        $this->ClienteCorreo = (!empty($data['ClienteCorreo'])) ? $data['ClienteCorreo'] : null;
        $this->FechaGenerado = (!empty($data['FechaGenerado'])) ? $data['FechaGenerado'] : null;

        $this->Rubro = (!empty($data['Rubro'])) ? $data['Rubro'] : null;
        $this->Rubro1 = (!empty($data['Rubro1'])) ? $data['Rubro1'] : null;
        $this->Rubro2 = (!empty($data['Rubro2'])) ? $data['Rubro2'] : null;
        $this->Rubro3 = (!empty($data['Rubro3'])) ? $data['Rubro3'] : null;
        $this->Rubro4 = (!empty($data['Rubro4'])) ? $data['Rubro4'] : null;
        $this->Rubro5 = (!empty($data['Rubro5'])) ? $data['Rubro5'] : null;
        $this->Rubro6 = (!empty($data['Rubro6'])) ? $data['Rubro6'] : null;
        $this->Rubro7 = (!empty($data['Rubro7'])) ? $data['Rubro7'] : null;
        $this->Rubro8 = (!empty($data['Rubro8'])) ? $data['Rubro8'] : null;
        $this->Rubro9 = (!empty($data['Rubro9'])) ? $data['Rubro9'] : null;
        $this->Rubro10 = (!empty($data['Rubro10'])) ? $data['Rubro10'] : null;
        $this->Rubro11 = (!empty($data['Rubro11'])) ? $data['Rubro11'] : null;
        $this->Rubro12 = (!empty($data['Rubro12'])) ? $data['Rubro12'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
}
