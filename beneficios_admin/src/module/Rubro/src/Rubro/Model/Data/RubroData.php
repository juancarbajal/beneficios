<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/09/15
 * Time: 06:57 PM
 */

namespace Rubro\Model\Data;

use Zend\InputFilter\InputFilter;

class RubroData
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
