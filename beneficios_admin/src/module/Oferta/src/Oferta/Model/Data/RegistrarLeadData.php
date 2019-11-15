<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/01/16
 * Time: 12:07 AM
 */

namespace Oferta\Model\Data;

class RegistrarLeadData
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
        $comboofe = array();
        $combofofe = array();

        try {
            foreach ($controller->getOfertaTable()->getOfertasTitulo(3) as $oferta) {
                $comboofe[$oferta->id] = $oferta->Titulo;
                $combofofe[$oferta->id] = $oferta->id;
            }
        } catch (\Exception $ex) {
            return $combo = array();
        }

        $this->formData['ofe'] = $comboofe;
        $this->filterData['ofe'] = array_keys($combofofe);
        return false;
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
