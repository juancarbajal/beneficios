<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 27/11/15
 * Time: 03:05 PM
 */

namespace Cliente\Model\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class FinalCsvFilter
{
    protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter($empresas = array())
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'empresa',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $empresas,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Empresas',
                                )
                            )
                        )
                    ),
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
