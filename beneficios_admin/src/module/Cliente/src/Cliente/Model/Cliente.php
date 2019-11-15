<?php

namespace Cliente\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Cliente
{
    public $id;
    public $BNF_TipoDocumento_id;
    public $Nombre;
    public $Apellido;
    public $NumeroDocumento;
    public $Genero;
    public $FechaNacimiento;
    public $Eliminado;
    public $UltimaConexion;
    public $idEmpresa;
    public $NombreComercial;
    public $NombreSegmento;
    public $NombreSubgrupo;
    public $FechaCreacion;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_TipoDocumento_id = (!empty($data['BNF_TipoDocumento_id'])) ? $data['BNF_TipoDocumento_id'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Apellido = (!empty($data['Apellido'])) ? $data['Apellido'] : null;
        $this->TipoDocumento = (!empty($data['TipoDocumento'])) ? $data['TipoDocumento'] : null;
        $this->NumeroDocumento = (!empty($data['NumeroDocumento'])) ? $data['NumeroDocumento'] : null;
        $this->Genero = (!empty($data['Genero'])) ? $data['Genero'] : null;
        $this->FechaNacimiento = (!empty($data['FechaNacimiento'])) ? $data['FechaNacimiento'] : null;
        $this->Eliminado = (!empty($data['Eliminado'])) ? $data['Eliminado'] : null;
        $this->Estado = (!empty($data['Estado'])) ? $data['Estado'] : null;
        $this->idEmpresa = (!empty($data['idEmpresa'])) ? $data['idEmpresa'] : null;
        $this->NombreComercial = (!empty($data['NombreComercial'])) ? $data['NombreComercial'] : null;
        $this->NombreSegmento = (!empty($data['NombreSegmento'])) ? $data['NombreSegmento'] : null;
        $this->NombreSubgrupo = (!empty($data['NombreSubgrupo'])) ? $data['NombreSubgrupo'] : null;
        $this->ClaseEmpresaCliente = (!empty($data['ClaseEmpresaCliente'])) ? $data['ClaseEmpresaCliente'] : null;
        $this->Total = (!empty($data['Total'])) ? $data['Total'] : null;
        $this->UltimaConexion = (!empty($data['UltimaConexion'])) ? $data['UltimaConexion'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter($val = 8, $option = null, $tipo = array())
    {
        if ($tipo == array()) {
            $tipo_id[1] = '0';
        }
        foreach ($tipo as $key => $dato) {
            $tipo_id[] = $key;
        }
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Nombre',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 50,
                                'messages' => array(
                                    'stringLengthTooLong' => 'El campo Nombre no debe exceder los 50 caracteres.'
                                ),
                            ),
                        ),
                        array(
                            'name' => 'Alpha',
                            'options' => array(
                                'allowWhiteSpace' => true,
                                'messages' => array(
                                    'notAlpha' => 'El campo Nombre solo puede aceptar Letras.'
                                ),
                            )
                        )
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Apellido',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 50,
                                'messages' => array(
                                    'stringLengthTooLong' => 'El campo Apellido no debe exceder los 50 caracteres.'
                                ),
                            ),
                        ),
                        array(
                            'name' => 'Alpha',
                            'options' => array(
                                'allowWhiteSpace' => true,
                                'messages' => array(
                                    'notAlpha' => 'El campo Apellido solo puede aceptar Letras.'
                                ),
                            )
                        )
                    ),
                )
            );

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
                                'haystack' => $tipo_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione el Tipo de Documento',
                                )
                            )
                        )
                    ),
                )
            );

            //número documento
            if ($option == 1) {
                $inputFilter->add(
                    array(
                        'name' => 'NumeroDocumento',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => $val,
                                    'max' => $val,
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
            } elseif ($option == 2) {
                $inputFilter->add(
                    array(
                        'name' => 'NumeroDocumento',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'max' => $val,
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
            } elseif ($option == 3) {
                $inputFilter->add(
                    array(
                        'name' => 'NumeroDocumento',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => $val,
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

            $inputFilter->add(
                array(
                    'name' => 'Genero',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 1,
                                'max' => 1
                            ),
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Género no puede quedar vacío.'
                            )
                        )
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'FechaNacimiento',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 10,
                                'max' => 10
                            ),
                        ),
                        array(
                            'name' => 'Cliente\Model\Validator\ValidDateBirthday',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 10,
                                'max' => 10
                            ),
                        ),
                        array(
                            'name' => 'Date',
                            'options' => array(
                                'messages' => array(
                                    'dateInvalidDate' => 'El campo Fecha de Nacimiento no esta en el formato correcto'
                                        . ' (AAAA-mm-dd).'
                                )
                            )
                        )
                    ),
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Eliminado',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
