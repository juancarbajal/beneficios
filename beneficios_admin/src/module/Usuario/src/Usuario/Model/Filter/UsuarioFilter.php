<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 17/09/15
 * Time: 10:09 PM
 */
namespace Usuario\Model\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UsuarioFilter
{

    protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter($tipousu = null, $tipo = null, $limit = 8, $datos = array(), $docs = array(), $empresa = array())
    {
        if ($empresa == array()) {
            $empresa_id = array(1 => 1);
        } else {
            foreach ($empresa as $key => $dato) {
                $empresa_id[] = $key;
            }
        }

        foreach ($datos as $key => $dato) {
            $asesor[] = $key;
        }
        foreach ($docs as $key => $dato) {
            $tipodocs[] = $key;
        }
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            //id
            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );
            //Tipo usuario (select)
            $inputFilter->add(
                array(
                    'name' => 'BNF_TipoUsuario_id',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Tipo de Usuario no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $asesor,
                                'messages' => array(
                                    'notInArray' => 'Seleccione el Tipo de Usuario',
                                )
                            )
                        )
                    ),
                )
            );
            if ($tipousu == 6 or $tipousu == 7 or $tipousu == 8) {
                //Empresa
                $inputFilter->add(
                    array(
                        'name' => 'BNF_Empresa_id',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Nombre Comercial o Razón Social o RUC no debe de quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $empresa_id,
                                    'messages' => array(
                                        'notInArray' => 'Tiene que Seleccionar una Empresa.'
                                    )
                                )
                            )
                        ),
                    )
                );
            }
            //Tipo documento (select)
            $inputFilter->add(
                array(
                    'name' => 'BNF_TipoDocumento_id',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Tipo de Documento no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $tipodocs,
                                'messages' => array(
                                    'notInArray' => 'Seleccione el Tipo de Documento',
                                )
                            )
                        )
                    ),
                )
            );
            //nombre
            $inputFilter->add(
                array(
                    'name' => 'Nombres',
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
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Nombres no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Alpha',
                            'options' => array(
                                'allowWhiteSpace' => true,
                                'messages' => array(
                                    'alphaInvalid' => 'Dato ingresado invalido',
                                    'notAlpha' => 'La entrada contiene caracteres no alfabéticos',
                                    'alphaStringEmpty' => 'La entrada es una cadena vacía',

                                ),
                            )
                        )
                    ),
                )
            );
            //contraseña
            $inputFilter->add(
                array(
                    'name' => 'Contrasenia',
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
                                'min' => 6,
                                'max' => 100,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Nombres no puede quedar vacío.'
                            )
                        ),
                    ),
                )
            );
            //apellidos
            $inputFilter->add(
                array(
                    'name' => 'Apellidos',
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
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Apellidos no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Alpha',
                            'options' => array(
                                'allowWhiteSpace' => true,
                                'messages' => array(
                                    'alphaInvalid' => 'Dato ingresado invalido',
                                    'notAlpha' => 'La entrada contiene caracteres no alfabéticos',
                                    'alphaStringEmpty' => 'La entrada es una cadena vacía',

                                ),
                            )
                        )
                    ),
                )
            );
            //email
            $inputFilter->add(
                array(
                    'name' => 'Correo',
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
                                    'emailAddressInvalidFormat' => 'La entrada no es una dirección de correo'
                                        . ' electrónico válida. Utilice el formato básico parte-local @ nombre'
                                        . ' de host',
                                    'emailAddressInvalidHostname' => "'%hostname%' no es un nombre de host válido para"
                                        . " la dirección de correo electrónico",
                                    'emailAddressInvalidMxRecord' => "'%hostname%' no parece tener ningún MX válido o"
                                        . " registros A para la dirección de correo electrónico",
                                    'emailAddressInvalidSegment' => "'%hostname%' no se encuentra en un segmento de red"
                                        . " enrutable. La dirección de correo electrónico no debe resolverse desde la"
                                        . " red pública",
                                    'emailAddressDotAtom' => "'%LocalPart%' no puede ser igualada contra el formato"
                                        . " de punto-átomo",
                                    'emailAddressQuotedString' => "'%LocalPart%' no puede ser igualada contra citada"
                                        . " cadena de formato",
                                    'emailAddressInvalidLocalPart' => "'%LocalPart%' no es una parte local válida para"
                                        . " la dirección de correo electrónico",
                                    'emailAddressLengthExceeded' => 'La entrada supera la longitud permitida',
                                    'hostnameInvalidHostname' => 'La entrada no coincide con la estructura prevista'
                                        . ' para un nombre de host DNS',
                                    'hostnameLocalNameNotAllowed' => 'La entrada parece ser un nombre de red local,'
                                        . ' pero los nombres de red locales no están permitidos'
                                )
                            )
                        )
                    ),
                )
            );
            //número documento
            if ($tipo == 1) {
                $inputFilter->add(
                    array(
                        'name' => 'NumeroDocumento',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => $limit,
                                    'max' => $limit,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                    )
                                ),
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Número de Documento no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'Digits',
                                'options' => array(
                                    'messages' => array(
                                        'notDigits' => 'La entrada debe contener sólo dígitos',
                                        'digitsStringEmpty' => 'La entrada es una cadena vacía',
                                        'digitsInvalid' => 'Tipo válida dado. String, entero o flotante esperada'
                                    )
                                )
                            )
                        ),
                    )
                );
            } elseif ($tipo == 2) {
                $inputFilter->add(
                    array(
                        'name' => 'NumeroDocumento',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'max' => $limit,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                    )
                                ),
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Número de Documento no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'Alnum',
                                'options' => array(
                                    'messages' => array(
                                        'alnumInvalid' => 'La entrada debe contener datos alfa numéricos',
                                        'notAlnum' => 'La entrada debe contener datos alfa numéricos',
                                        'alnumStringEmpty' => 'La entrada es una cadena vacía'
                                    )
                                )
                            )
                        ),
                    )
                );
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'NumeroDocumento',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => $limit,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                    )
                                ),
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Número de Documento no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'Alnum',
                                'options' => array(
                                    'messages' => array(
                                        'alnumInvalid' => 'La entrada debe contener datos alfa numéricos',
                                        'notAlnum' => 'La entrada debe contener datos alfa numéricos',
                                        'alnumStringEmpty' => 'La entrada es una cadena vacía'
                                    )
                                )
                            )
                        ),
                    )
                );
            }
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

    public function getInputFilterE($tipousu = null, $tipo = null, $limit = 8, $datos = array(), $docs = array(), $empresa = array())
    {
        if ($empresa == array()) {
            $empresa_id = array(1 => 1);
        } else {
            foreach ($empresa as $key => $dato) {
                $empresa_id[] = $key;
            }
        }

        foreach ($datos as $key => $dato) {
            $asesor[] = $key;
        }
        foreach ($docs as $key => $dato) {
            $tipodocs[] = $key;
        }
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            //id
            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );
            //Tipo usuario (select)
            $inputFilter->add(
                array(
                    'name' => 'BNF_TipoUsuario_id',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Tipo de Usuario no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $asesor,
                                'messages' => array(
                                    'notInArray' => 'Seleccione el Tipo de Usuario',
                                )
                            )
                        )
                    ),
                )
            );
            if ($tipousu == 6 or $tipousu == 7 or $tipousu == 8) {
                //Empresa
                $inputFilter->add(
                    array(
                        'name' => 'BNF_Empresa_id',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Nombre Comercial o Razón Social o RUC no debe de quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $empresa_id,
                                    'messages' => array(
                                        'notInArray' => 'Tiene que Seleccionar una Empresa.'
                                    )
                                )
                            )
                        ),
                    )
                );
            }
            //Tipo documento (select)
            $inputFilter->add(
                array(
                    'name' => 'BNF_TipoDocumento_id',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Tipo de Documento no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $tipodocs,
                                'messages' => array(
                                    'notInArray' => 'Seleccione el Tipo de Documento',
                                )
                            )
                        )
                    ),
                )
            );
            //nombre
            $inputFilter->add(
                array(
                    'name' => 'Nombres',
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
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Nombres no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Alpha',
                            'options' => array(
                                'allowWhiteSpace' => true,
                                'messages' => array(
                                    'alphaInvalid' => 'Dato ingresado invalido',
                                    'notAlpha' => 'La entrada contiene caracteres no alfabéticos',
                                    'alphaStringEmpty' => 'La entrada es una cadena vacía',

                                ),
                            )
                        )
                    ),
                )
            );
            //apellidos
            $inputFilter->add(
                array(
                    'name' => 'Apellidos',
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
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Apellidos no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'Alpha',
                            'options' => array(
                                'allowWhiteSpace' => true,
                                'messages' => array(
                                    'alphaInvalid' => 'Dato ingresado invalido',
                                    'notAlpha' => 'La entrada contiene caracteres no alfabéticos',
                                    'alphaStringEmpty' => 'La entrada es una cadena vacía',

                                ),
                            )
                        )
                    ),
                )
            );
            //email
            $inputFilter->add(
                array(
                    'name' => 'Correo',
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
                                    'emailAddressInvalidFormat' => 'La entrada no es una dirección de correo'
                                        . ' electrónico válida. Utilice el formato básico parte-local @ nombre'
                                        . ' de host',
                                    'emailAddressInvalidHostname' => "'%hostname%' no es un nombre de host válido para"
                                        . " la dirección de correo electrónico",
                                    'emailAddressInvalidMxRecord' => "'%hostname%' no parece tener ningún MX válido o"
                                        . " registros A para la dirección de correo electrónico",
                                    'emailAddressInvalidSegment' => "'%hostname%' no se encuentra en un segmento de red"
                                        . " enrutable. La dirección de correo electrónico no debe resolverse desde la"
                                        . " red pública",
                                    'emailAddressDotAtom' => "'%LocalPart%' no puede ser igualada contra el formato"
                                        . " de punto-átomo",
                                    'emailAddressQuotedString' => "'%LocalPart%' no puede ser igualada contra citada"
                                        . " cadena de formato",
                                    'emailAddressInvalidLocalPart' => "'%LocalPart%' no es una parte local válida para"
                                        . " la dirección de correo electrónico",
                                    'emailAddressLengthExceeded' => 'La entrada supera la longitud permitida',
                                    'hostnameInvalidHostname' => 'La entrada no coincide con la estructura prevista'
                                        . ' para un nombre de host DNS',
                                    'hostnameLocalNameNotAllowed' => 'La entrada parece ser un nombre de red local,'
                                        . ' pero los nombres de red locales no están permitidos'
                                )
                            )
                        )
                    ),
                )
            );
            if ($tipo == 1) {
                $inputFilter->add(
                    array(
                        'name' => 'NumeroDocumento',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => $limit,
                                    'max' => $limit,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                    )
                                ),
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Número de Documento no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'Digits',
                                'options' => array(
                                    'messages' => array(
                                        'notDigits' => 'La entrada debe contener sólo dígitos',
                                        'digitsStringEmpty' => 'La entrada es una cadena vacía',
                                        'digitsInvalid' => 'Tipo válida dado. String, entero o flotante esperada'
                                    )
                                )
                            )
                        ),
                    )
                );
            } elseif ($tipo == 2) {
                $inputFilter->add(
                    array(
                        'name' => 'NumeroDocumento',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'max' => $limit,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                    )
                                ),
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Número de Documento no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'Alnum',
                                'options' => array(
                                    'messages' => array(
                                        'alnumInvalid' => 'La entrada debe contener datos alfa numéricos',
                                        'notAlnum' => 'La entrada debe contener datos alfa numéricos',
                                        'alnumStringEmpty' => 'La entrada es una cadena vacía'
                                    )
                                )
                            )
                        ),
                    )
                );
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'NumeroDocumento',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => $limit,
                                    'messages' => array(
                                        'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                        'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                    )
                                ),
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Número de Documento no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'Alnum',
                                'options' => array(
                                    'messages' => array(
                                        'alnumInvalid' => 'La entrada debe contener datos alfa numéricos',
                                        'notAlnum' => 'La entrada debe contener datos alfa numéricos',
                                        'alnumStringEmpty' => 'La entrada es una cadena vacía'
                                    )
                                )
                            )
                        ),
                    )
                );
            }
            //contraseña
            $inputFilter->add(
                array(
                    'name' => 'Contrasenia',
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
                                'min' => 6,
                                'max' => 100,
                                'messages' => array(
                                    'stringLengthTooShort' => 'La entrada es menor que %min% caracteres de largo',
                                    'stringLengthTooLong' => 'La entrada es de más de %max% caracteres'
                                )
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Nombres no puede quedar vacío.'
                            )
                        ),
                    ),
                )
            );
            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
