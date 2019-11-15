<?php
/**
 * Created by PhpStorm.
 * User: janaqlap2
 * Date: 22/01/16
 * Time: 12:11 PM
 */

namespace Categoria\Model\Data;

class BuscarCategoriaData
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
        $combo = array();

        try {
            $paises = $controller->getPaisTable()->fetchAll();
            foreach ($paises as $dato) {
                $combo[$dato->id] = $dato->NombrePais;
            }
        } catch (\Exception $ex) {
            $combo = array();
        }

        $this->formData['pais'] = $combo;

        $this->filterData['pais'] = array_keys($combo);
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
