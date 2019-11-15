<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/09/15
 * Time: 06:57 PM
 */

namespace Oferta\Model\Filter;

use Zend\InputFilter\InputFilter;

class OfertaFilter
{
    protected $inputFilter;

    public function getInputFilter($filter, $max = 100, $min = 1, $type = null, $action = 0, $disabled = false, $tipoOferta = null)
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            //*********** Datos Generales ***********//
            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            if ($disabled) {
                $inputFilter->add(
                    array(
                        'name' => 'Empresa',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int')
                        ),
                    )
                );
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'Empresa',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'Int')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $filter['emp'],
                                    'messages' => array(
                                        'notInArray' => "El valor seleccionado, no es válido."
                                    )
                                )
                            )
                        )
                    )
                );
            }

            if ($tipoOferta != "Split") {
                $inputFilter->add(
                    array(
                        'name' => 'Stock',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'Int')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'IsInt',
                                'options' => array(
                                    'messages' => array(
                                        'notInt' => 'El valor ingresado en Stock, no es un número entero.',
                                        'intInvalid' => 'El valor ingresado en Stock, no es un número.'
                                    )
                                )
                            ),
                            array(
                                'name' => 'LessThan',
                                'options' => array(
                                    'max' => $max,
                                    'inclusive' => true,
                                    'messages' => array(
                                        'notLessThan' => "El valor ingresado, no es menor que %max%",
                                        'notLessThanInclusive' => "El valor ingresado, no es menor o igual que %max%",
                                    )
                                )
                            ),
                            array(
                                'name' => 'GreaterThan',
                                'options' => array(
                                    'min' => $min,
                                    'inclusive' => true,
                                    'messages' => array(
                                        'notGreaterThan' => "El valor ingresado no es mayor que %min%",
                                        'notGreaterThanInclusive' => "El valor ingresado no es mayor o igual que %min%"
                                    )
                                )
                            )
                        )
                    )
                );
            }

            if ($action == 2) {
                $inputFilter->add(
                    array(
                        'name' => 'StockInicial',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'Int')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'IsInt',
                                'options' => array(
                                    'messages' => array(
                                        'notInt' => 'El valor ingresado en Stock, no es un número entero.',
                                        'intInvalid' => 'El valor ingresado en Stock, no es un número.'
                                    )
                                )
                            ),
                            array(
                                'name' => 'GreaterThan',
                                'options' => array(
                                    'min' => $min,
                                    'inclusive' => true,
                                    'messages' => array(
                                        'notGreaterThan' => "El valor ingresado no es mayor que %min%",
                                        'notGreaterThanInclusive' => "El valor ingresado no es mayor o igual que %min%"
                                    )
                                )
                            )
                        )
                    )
                );
            }

            $inputFilter->add(
                array(
                    'name' => 'Tipo',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['tip'],
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
                    'name' => 'Titulo',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooLong' => 'La Razon Social no de Tener mas de 255 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'El Titulo es requerido y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'BNF_TipoBeneficio_id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['tib'],
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
                    'name' => 'Descripcion',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooLong' => 'La Descripción no debe tener más de 255 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'La Descripción es requerida y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'CondicionesUso',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'messages' => array(
                                    'stringLengthTooLong' => 'Las Condiciones de Uso no debe ' .
                                        'tener más de 255 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'Las Condiciones de Uso son requeridas y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Direccion',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooLong' => 'La Dirección no de tener más de 255 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'La Dirección es requerida y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Telefono',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 50,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'El Telefono es requerido y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Premium',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'No seleccionó el estado Premium.'
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Correo',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
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

            $inputFilter->add(
                array(
                    'name' => 'Rubro',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['rub'],
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
                    'name' => 'Pais',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['pais'],
                                'messages' => array(
                                    'notInArray' => "El valor seleccionado, no es válido."
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'No se Selecciono ningún País.'
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Departamento',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'No se selecciono ningún Departamento.'
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Categoria',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'No se selecciono ninguna Categoria.'
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Campania',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'No se Selecciono ninguna Campaña.'
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Segmento',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'No se Selecciono ningún Segmento.'
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Pais',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $filter['pais'],
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
                    'name' => 'Estado',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => array('Pendiente', 'Publicado', 'Caducado'),
                                'messages' => array(
                                    'notInArray' => "El valor seleccionado, no es válido."
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'No se Selecciono ningún Estado.'
                                )
                            )
                        )
                    )
                )
            );

            if ($tipoOferta == "Split" && ($type == 1 || $type == 2)) {
                $inputFilter->add(
                    array(
                        'name' => 'FechaFinVigencia',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'max' => 10,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                    )
                                ),
                            ),
                            array(
                                'name' => 'Date',
                                'options' => array(
                                    'messages' => array(
                                        'dateInvalid' => 'Dato ingresado incorrecto',
                                        'dateInvalidDate' => 'La entrada no parece ser una fecha válida',
                                        'dateFalseFormat' => 'La entrada no se ajusta al formato de fecha %format%',
                                    )
                                )
                            ),
                            array(
                                'name' => 'GreaterThan',
                                'options' => array(
                                    'min' => '2014-01-01',
                                    'messages' => array(
                                        'notGreaterThan' => "La entrada no es mayor que '%min%'",
                                        'notGreaterThanInclusive' => "La entrada no es mayor o igual a '%min%'",
                                    )
                                )
                            )
                        ),
                    )
                );

                $inputFilter->add(
                    array(
                        'name' => 'CorreoContacto',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
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
                                        'emailAddressInvalidFormat' => 'La entrada no es una dirección de ' .
                                            'email válida. Utilice el formato básico ejemplo@email.com',
                                        'emailAddressInvalidHostname' => "'%hostname%' no es un nombre de host válido" .
                                            " para la dirección de correo electrónico",
                                    ),
                                )
                            ),
                        ),
                    )
                );
            } else if ($type == 3 || $type == null) {
                $inputFilter->add(
                    array(
                        'name' => 'FechaFinVigencia',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'max' => 10,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                    )
                                ),
                            ),
                            array(
                                'name' => 'Date',
                                'options' => array(
                                    'messages' => array(
                                        'dateInvalid' => 'Dato ingresado incorrecto',
                                        'dateInvalidDate' => 'La entrada no parece ser una fecha válida',
                                        'dateFalseFormat' => 'La entrada no se ajusta al formato de fecha %format%',
                                    )
                                )
                            ),
                            array(
                                'name' => 'GreaterThan',
                                'options' => array(
                                    'min' => '2014-01-01',
                                    'messages' => array(
                                        'notGreaterThan' => "La entrada no es mayor que '%min%'",
                                        'notGreaterThanInclusive' => "La entrada no es mayor o igual a '%min%'",
                                    )
                                )
                            )
                        ),
                    )
                );

                $inputFilter->add(
                    array(
                        'name' => 'CorreoContacto',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
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
                                        'emailAddressInvalidFormat' => 'La entrada no es una dirección de ' .
                                            'email válida. Utilice el formato básico ejemplo@email.com',
                                        'emailAddressInvalidHostname' => "'%hostname%' no es un nombre de host válido" .
                                            " para la dirección de correo electrónico",
                                    ),
                                )
                            ),
                        ),
                    )
                );
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'FechaFinVigencia',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'max' => 10,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                    )
                                ),
                            ),
                            array(
                                'name' => 'Date',
                                'options' => array(
                                    'messages' => array(
                                        'dateInvalid' => 'Dato ingresado incorrecto',
                                        'dateInvalidDate' => 'La entrada no parece ser una fecha válida',
                                        'dateFalseFormat' => 'La entrada no se ajusta al formato de fecha %format%',
                                    )
                                )
                            ),
                            array(
                                'name' => 'GreaterThan',
                                'options' => array(
                                    'min' => '2014-01-01',
                                    'messages' => array(
                                        'notGreaterThan' => "La entrada no es mayor que '%min%'",
                                        'notGreaterThanInclusive' => "La entrada no es mayor o igual a '%min%'",
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Vigencia no puede quedar vacío.'
                                )
                            ),
                        ),
                    )
                );

                $inputFilter->add(
                    array(
                        'name' => 'CorreoContacto',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
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
                                        'emailAddressInvalidFormat' => 'La entrada no es una dirección de ' .
                                            'email válida. Utilice el formato básico ejemplo@email.com',
                                        'emailAddressInvalidHostname' => "'%hostname%' no es un nombre de host válido" .
                                            " para la dirección de correo electrónico",
                                    ),
                                )
                            ),
                        ),
                    )
                );
            }

            $inputFilter->add(
                array(
                    'name' => 'FechaInicioPublicacion',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 10,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Periodo de Publicación no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'messages' => array(
                                    'dateInvalid' => 'Dato ingresado incorrecto',
                                    'dateInvalidDate' => 'La entrada no parece ser una fecha válida',
                                    'dateFalseFormat' => 'La entrada no se ajusta al formato de fecha %format%',
                                )
                            )
                        ),
                        array(
                            'name' => 'GreaterThan',
                            'options' => array(
                                'min' => '2014-01-01',
                                'messages' => array(
                                    'notGreaterThan' => "La entrada no es mayor que '%min%'",
                                    'notGreaterThanInclusive' => "La entrada no es mayor o igual a '%min%'",
                                )
                            )
                        )
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'FechaFinPublicacion',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'max' => 10,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres',
                                )
                            ),
                        ),
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'messages' => array(
                                    'dateInvalid' => 'Dato ingresado incorrecto',
                                    'dateInvalidDate' => 'La entrada no parece ser una fecha válida',
                                    'dateFalseFormat' => 'La entrada no se ajusta al formato de fecha %format%',
                                )
                            )
                        ),
                        array(
                            'name' => 'GreaterThan',
                            'options' => array(
                                'min' => '2014-01-01',
                                'messages' => array(
                                    'notGreaterThan' => "La entrada no es mayor que '%min%'",
                                    'notGreaterThanInclusive' => "La entrada no es mayor o igual a '%min%'",
                                )
                            )
                        ),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DescargaMaximaDia',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'Digits',
                            'options' => array(
                                'messages' => array(
                                    'notDigits' => 'La entrada debe contener sólo dígitos.',
                                    'digitsStringEmpty' => 'La entrada es una cadena vacía',
                                    'digitsInvalid' => 'Tipo de dato inválido.'
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'Las Descargas Máxima son requeridas y no puede quedar vacío.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'CondicionesTebca',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StringTrim')
                    )
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
