<?php
/**
 * Created by PhpStorm.
 * User: janaqlap2
 * Date: 22/01/16
 * Time: 12:11 PM
 */

namespace Oferta\Model\Data;

class BuscarOfertaConsumidaData
{
    protected $formData;
    protected $filterData;

    public function __construct($controller, $empresa = null)
    {
        $this->formData = array();
        $this->filterData = array();
        $this->init($controller, $empresa);
    }

    public function init($controller, $empresa = null)
    {
        $comboofe = array();
        $combofofe = array();
        $comboest = array(
            'Pendiente' => 'Pendiente',
            'Publicado' => 'Publicado',
            'Caducado' => 'Caducado',
        );

        try {
            foreach ($controller->getOfertaTable()->getOfertasTitulo(null, $empresa) as $dato) {
                $comboofe[$dato->id] = $dato->Titulo;
                $combofofe[$dato->id] = $dato->id;
            }
        } catch (\Exception $ex) {
            $comboofe = array();
            $combofofe = array();
        }

        $this->formData['ofe'] = $comboofe;
        $this->formData['est'] = $comboest;

        $this->filterData['ofe'] = array_keys($combofofe);
        $this->filterData['est'] = array_keys($comboest);
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
