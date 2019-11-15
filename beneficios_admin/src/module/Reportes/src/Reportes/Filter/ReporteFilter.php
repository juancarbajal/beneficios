<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 10/09/15
 * Time: 12:59 PM
 */

namespace Reportes\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class ReporteFilter
{
    protected $inputFilter;

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                    array(
                        'name' => 'Emails',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Emails no puede quedar vacío.'
                                )
                            ),
                            array(
                                "name" => "Regex",
                                "options" => array(
                                    "pattern" => "/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}(,[\w-\.]+@([\w-]+\.)+[\w-]{2,4})+$/",
                                    "messages" => array(
                                        "regexInvalid" => "Regex es inválido.",
                                        "regexNotMatch" => "El Email ingresado no es válido.",
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
