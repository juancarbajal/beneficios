<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 25/01/16
 * Time: 11:43 AM
 */

namespace Puntos\Model\Filter;

use Zend\InputFilter\InputFilter;

class DeliveryFilter
{
    protected $inputFilter;

    public function getInputFilter($filter)
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
                                'haystack' => $filter,
                                'messages' => array(
                                    'notInArray' => "El valor seleccionado, no es válido."
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'CorreoContactoDelivery',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Correo no puede quedar vacío.'
                            )
                        ),
                        array(
                            "name" => "Regex",
                            "options" => array(
                                "pattern" => "/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/",
                                "messages" => array(
                                    "regexInvalid" => "Regex es inválido.",
                                    "regexNotMatch" => "El Correo ingresado no es válido.",
                                    "regexErrorous" => "Error interno al validar el correo."
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
