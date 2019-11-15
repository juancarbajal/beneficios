<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/09/15
 * Time: 06:57 PM
 */

namespace Paquete\Model\Data;

use Zend\InputFilter\InputFilter;

class BuscarPaqueteData
{
    protected $formData;
    protected $filterData;


    public function __construct( $controller){
        $this->formData = array();
        $this->filterData = array();
        $this->init($controller);
    }

    public function init($controller){
        $comboemp = array();
        $combotp = array();
        $combotb = array();
        $comborub = array();
        $combocat = array();
        $combocam = array();
        $combopais = array();
        $comboubig = array();
        $comboseg = array();

        $combofemp = array();
        $comboftp = array();
        $comboftb = array();
        $combofrub = array();
        $combofcat = array();
        $combofcam = array();
        $combofpais = array();
        $combofubig = array();
        $combofseg = array();

        try {
            foreach ($controller->getEmpresaTable()->getPaqueteEmpresas() as $empresa) {
                $comboemp[$empresa->id] = $empresa->NombreComercial . ' - ' . $empresa->RazonSocial .
                    ' - ' . $empresa->Ruc;
                $combofemp[$empresa->id] = [$empresa->id];
            }
        } catch (\Exception $ex) {
            $comboemp = array();
        }

        try {
            foreach ($controller->getTipoPaqueteTable()->fetchAll() as $tipospaq) {
                $combotp[$tipospaq->id] = $tipospaq->NombreTipoPaquete;
                $comboftp[$tipospaq->id] = $tipospaq->id;
            }
        } catch (\Exception $ex) {
            $combotp = array();
        }

        try {
            foreach ($controller->getTipoBeneficioTable()->fetchAll() as $tiposben) {
                $combotb[$tiposben->id] = $tiposben->NombreBeneficio;
                $comboftb[$tiposben->id] = $tiposben->id;
            }
        } catch (\Exception $ex) {
            $combotb = array();
        }

        try {
            foreach ($controller->getRubroTable()->fetchAll() as $rubro) {
                $comborub[$rubro->id] = $rubro->Nombre;
                $combofrub[$rubro->id] = $rubro->id;
            }
        } catch (\Exception $ex) {
            $comborub = array();
        }

        try {
            foreach ($controller->getPaisTable()->fetchAll() as $pais) {
                $combopais[$pais->id] = $pais->NombrePais;
                $combofpais[$pais->id] = $pais->id;
            }
        } catch (\Exception $ex) {
            $combopais = array();
        }



        try {
            foreach ($controller->getCategoriaTable()->fetchAll() as $categoria) {
                $combocat[$categoria->id] = $categoria->Nombre;
                $combofcat[$categoria->id] = $categoria->id;
            }
        } catch (\Exception $ex) {
            $combocat = array();
        }

        try {
            foreach ($controller->getCampaniaTable()->fetchAll() as $campaña) {
                $combocam[$campaña->id] = $campaña->Nombre;
                $combofcam[$campaña->id] = $campaña->id;
            }
        } catch (\Exception $ex) {
            $combocam = array();
        }

        try {
            foreach ($controller->getUbigeoTable()->fetchAllDepartament() as $dato) {
                $comboubig[$dato->id] = $dato->Nombre;
                $combofubig[$dato->id] = $dato->id;
            }
        } catch (\Exception $ex) {
            $comboubig = array();
        }



        try {
            foreach ($controller->getSegmentoTable()->fetchAll() as $dato) {
                $comboseg[$dato->id] = $dato->Nombre;
                $combofseg[$dato->id] = $dato->id;
            }
        } catch (\Exception $ex) {
            $comboseg = array();
        }

        $this->formData['emp'] = $comboemp;
        $this->formData['tip'] = $combotp;
        $this->formData['tib'] = $combotb;
        $this->formData['rub'] = $comborub;
        $this->formData['cat'] = $combocat;
        $this->formData['cam'] = $combocam;
        $this->formData['pais'] = $combopais;
        $this->formData['ubig'] = $comboubig;
        $this->formData['seg'] = $comboseg;

        $this->filterData['emp'] = array_keys($combofemp);
        $this->filterData['tip'] = array_keys($comboftp);
        $this->filterData['tib'] = array_keys($comboftb);
        $this->filterData['rub'] = array_keys($combofrub);
        $this->filterData['cat'] = array_keys($combofcat);
        $this->filterData['cam'] = array_keys($combofcam);
        $this->filterData['pais'] = array_keys($combofpais);
        $this->filterData['ubig'] = array_keys($combofubig);
        $this->filterData['seg'] = $comboseg;
    }

    /**
     * @return mixed
     */
    public function getFormData()
    {
        return $this->formData;
    }

    /**
     * @return mixed
     */
    public function getFilterData()
    {
        return $this->filterData;
    }

}
