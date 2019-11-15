<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 17/08/16
 * Time: 05:55 PM
 */

namespace Premios\Model\Filter;


use Zend\InputFilter\InputFilter;

class SegmentosPremiosFilter
{
    protected $inputFilter;

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'NombreSegmento',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
                            )
                        ),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'CantidadPremios',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
                            )
                        ),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'CantidadPersonas',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
                            )
                        ),
                    )
                )
            );

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}