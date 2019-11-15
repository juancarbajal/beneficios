<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 27/11/15
 * Time: 03:05 PM
 */

namespace Referido\Model\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Hostname;

class ConfiguracionRedFilter
{
    protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'repeticion_01',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
                            )
                        ),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'repeticion_02',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
                            )
                        ),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'repeticion_03',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo no puede quedar vacío'
                            )
                        ),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'banner_link_ref',
                    'required' => false,
                    'validators' => array(
                        array(
                            "name" => "Regex",
                            "options" => array(
                                "pattern" => "/((?:https?|ftp):\/\/(?:\S*?\.\S*?)(?:[\s)\[\]{},;\"\':<]|\.\s|$))/",
                                "messages" => array(
                                    "regexInvalid" => "Regex es inválido.",
                                    "regexNotMatch" => "El formato es incorrecto",
                                    "regexErrorous" => "Se ha producido un error interno"
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'correo_ref',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 45,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Correo no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'EmailAddress',
                            'options' => array(
                                'messages' => array(
                                    'emailAddressInvalid' => 'fromato no válido.',
                                    'emailAddressInvalidFormat' => 'La entrada no es una dirección de email válida.' .
                                        ' Utilice el formato básico ejemplo@email.com',
                                    'emailAddressInvalidHostname' => "'%hostname%' no es un nombre de host válido" .
                                        " para la dirección de correo electrónico"
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
