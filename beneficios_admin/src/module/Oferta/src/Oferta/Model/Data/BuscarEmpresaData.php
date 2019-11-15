<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/09/15
 * Time: 06:57 PM
 */

namespace Oferta\Model\Data;

use Zend\InputFilter\InputFilter;

class BuscarEmpresaData
{
    protected $formData;
    protected $filterData;

    public function __construct($controller)
    {
        $this->formData = array();
        $this->filterData = array();
        $this->init($controller);
    }

    public function init($controller)
    {
        $comboemp = array();
        $comboofe = array();


        $combofemp = array();
        $combofofe = array();


        try {
            foreach ($controller->getEmpresaTable()->getEmpresaCli() as $empresa) {
                $comboemp[$empresa->id] = $empresa->NombreComercial . ' - ' . $empresa->RazonSocial;
                $combofemp[$empresa->id] = $empresa->id;
            }
        } catch (\Exception $ex) {
            return $comboemp = array();
        }

        try {
            foreach ($controller->getOfertaTable()->fetchAll() as $oferta) {
                $comboofe[$oferta->id] = $oferta->Titulo;
                $combofofe[$oferta->id] = $oferta->id;
            }
        } catch (\Exception $ex) {
            return $combo = array();
        }

        $this->formData['emp'] = $comboemp;
        $this->formData['ofe'] = $comboofe;

        $this->filterData['emp'] = array_keys($combofemp);
        $this->filterData['ofe'] = array_keys($combofofe);

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
