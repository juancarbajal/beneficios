<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/01/16
 * Time: 11:43 AM
 */

namespace Oferta\Model\Filter;

use Zend\InputFilter\InputFilter;

class CargarCodigosFilter
{
    protected $inputFilter;

    public function getInputFilter($filter, $data)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'Oferta',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['ofe'],
                                'messages' => array(
                                    'notInArray' => "El valor seleccionado, no es válido."
                                )
                            )
                        )
                    )
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
