<?php

namespace Empresa\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Empresa
{
    public $id;
    public $BNF_Usuario_id;
    public $BNF_TipoDocumento_id;
    public $BNF_Ubigeo_id_envio;
    public $BNF_Ubigeo_id_legal;
    public $BNF_Ubigeo_id_empresa;
    public $NombreComercial;
    public $RazonSocial;
    public $ApellidoPaterno;
    public $ApellidoMaterno;
    public $checkboxLogo;
    public $checkboxLogoBeneficio;
    public $checkboxMoney;
    public $checkboxTotalPuntos;
    public $Nombre;
    public $Ruc;
    public $Descripcion;
    public $RepresentanteLegal;
    public $RepresentanteNumeroDocumento;
    public $DireccionLegal;
    public $DireccionLegalDetalle;
    public $DireccionEnvio;
    public $DireccionEnvioDetalle;
    public $DireccionEmpresa;
    public $DireccionEmpresaDetalle;
    public $HoraAtencion;
    public $HoraAtencionInicio;
    public $HoraAtencionFin;
    public $PersonaAtencion;
    public $CargoPersonaAtencion;
    public $Telefono;
    public $Celular;
    public $CorreoPersonaAtencion;
    public $Logo;
    public $IdSap;
    public $FechaCreacion;
    public $FechaActualizacion;
    public $ClaseEmpresaCliente;
    public $Slug;
    public $SitioWeb;
    public $NombreContacto;
    public $CorreoContacto;
    public $TelefonoContacto;
    public $HoraAtencionContacto;
    public $HoraAtencionInicioContacto;
    public $HoraAtencionFinContacto;
    public $Proveedor;
    public $Cliente;
    public $SubDominio;
    public $Logo_sitio;
    public $Color;
    public $Color_menu;
    public $Color_hover;

    public $CNombres;
    public $CApellidos;
    public $TipoDocumento;
    public $TipoEmpresa;
    public $NombreTipoEmpresa;
    public $TEliminado;
  //  public $Empresa;

    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->id = (!empty($data['id'])) ? $data['id'] : null;
        $this->BNF_Usuario_id = (!empty($data['BNF_Usuario_id'])) ? $data['BNF_Usuario_id'] : null;
        $this->BNF_TipoDocumento_id = (!empty($data['BNF_TipoDocumento_id'])) ? $data['BNF_TipoDocumento_id'] : null;
        $this->BNF_Ubigeo_id_envio = (!empty($data['BNF_Ubigeo_id_envio'])) ? $data['BNF_Ubigeo_id_envio'] : null;
        $this->BNF_Ubigeo_id_legal = (!empty($data['BNF_Ubigeo_id_legal'])) ? $data['BNF_Ubigeo_id_legal'] : null;
        $this->BNF_Ubigeo_id_empresa = (!empty($data['BNF_Ubigeo_id_empresa'])) ? $data['BNF_Ubigeo_id_empresa'] : null;
        $this->NombreComercial = (!empty($data['NombreComercial'])) ? $data['NombreComercial'] : null;
        $this->RazonSocial = (!empty($data['RazonSocial'])) ? $data['RazonSocial'] : null;
        $this->ApellidoPaterno = (!empty($data['ApellidoPaterno'])) ? $data['ApellidoPaterno'] : null;
        $this->ApellidoMaterno = (!empty($data['ApellidoMaterno'])) ? $data['ApellidoMaterno'] : null;
        $this->Nombre = (!empty($data['Nombre'])) ? $data['Nombre'] : null;
        $this->Ruc = (!empty($data['Ruc'])) ? $data['Ruc'] : null;
        $this->Descripcion = (!empty($data['Descripcion'])) ? $data['Descripcion'] : null;
        $this->RepresentanteLegal = (!empty($data['RepresentanteLegal'])) ? $data['RepresentanteLegal'] : null;
        $this->RepresentanteNumeroDocumento = (!empty($data['RepresentanteNumeroDocumento']))
            ? $data['RepresentanteNumeroDocumento'] : null;
        $this->DireccionLegal = (!empty($data['DireccionLegal'])) ? $data['DireccionLegal'] : null;
        $this->DireccionLegalDetalle = (!empty($data['DireccionLegalDetalle'])) ? $data['DireccionLegalDetalle'] : null;
        $this->DireccionEnvio = (!empty($data['DireccionEnvio'])) ? $data['DireccionEnvio'] : null;
        $this->DireccionEnvioDetalle = (!empty($data['DireccionEnvioDetalle'])) ? $data['DireccionEnvioDetalle'] : null;
        $this->DireccionEmpresa = (!empty($data['DireccionEmpresa'])) ? $data['DireccionEmpresa'] : null;
        $this->DireccionEmpresaDetalle = (!empty($data['DireccionEmpresaDetalle']))
            ? $data['DireccionEmpresaDetalle'] : null;
        $this->HoraAtencion = (!empty($data['HoraAtencion'])) ? $data['HoraAtencion'] : null;
        $this->HoraAtencionInicio = (!empty($data['HoraAtencionInicio'])) ? $data['HoraAtencionInicio'] : null;
        $this->HoraAtencionFin = (!empty($data['HoraAtencionFin'])) ? $data['HoraAtencionFin'] : null;
        $this->PersonaAtencion = (!empty($data['PersonaAtencion'])) ? $data['PersonaAtencion'] : null;
        $this->CargoPersonaAtencion = (!empty($data['CargoPersonaAtencion'])) ? $data['CargoPersonaAtencion'] : null;
        $this->Telefono = (!empty($data['Telefono'])) ? $data['Telefono'] : null;
        $this->Celular = (!empty($data['Celular'])) ? $data['Celular'] : null;
        $this->CorreoPersonaAtencion = (!empty($data['CorreoPersonaAtencion'])) ? $data['CorreoPersonaAtencion'] : null;
        $this->Logo = (!empty($data['Logo'])) ? $data['Logo'] : null;
        $this->IdSap = (!empty($data['IdSap'])) ? $data['IdSap'] : null;
        $this->FechaCreacion = (!empty($data['FechaCreacion'])) ? $data['FechaCreacion'] : null;
        $this->FechaActualizacion = (!empty($data['FechaActualizacion'])) ? $data['FechaActualizacion'] : null;
        $this->ClaseEmpresaCliente = (!empty($data['ClaseEmpresaCliente'])) ? $data['ClaseEmpresaCliente'] : null;
        $this->Slug = (!empty($data['Slug'])) ? $data['Slug'] : null;
        $this->SitioWeb = (!empty($data['SitioWeb'])) ? $data['SitioWeb'] : null;
        $this->NombreContacto = (!empty($data['NombreContacto'])) ? $data['NombreContacto'] : null;
        $this->CorreoContacto = (!empty($data['CorreoContacto'])) ? $data['CorreoContacto'] : null;
        $this->TelefonoContacto = (!empty($data['TelefonoContacto'])) ? $data['TelefonoContacto'] : null;
        $this->HoraAtencionContacto = (!empty($data['HoraAtencionContacto'])) ? $data['HoraAtencionContacto'] : null;
        $this->HoraAtencionInicioContacto = (!empty($data['HoraAtencionInicioContacto']))
            ? $data['HoraAtencionInicioContacto'] : null;
        $this->HoraAtencionFinContacto = (!empty($data['HoraAtencionFinContacto']))
            ? $data['HoraAtencionFinContacto'] : null;
        $this->Proveedor = (!empty($data['Proveedor'])) ? $data['Proveedor'] : 0;
        $this->Cliente = (!empty($data['Cliente'])) ? $data['Cliente'] : 0;
        $this->SubDominio = (!empty($data['SubDominio'])) ? $data['SubDominio'] : null;
        $this->Logo_sitio = (!empty($data['Logo_sitio'])) ? $data['Logo_sitio'] : null;
        $this->Color = (!empty($data['Color'])) ? $data['Color'] : null;
        $this->Color_menu = (!empty($data['Color_menu'])) ? $data['Color_menu'] : null;
        $this->Color_hover = (!empty($data['Color_hover'])) ? $data['Color_hover'] : null;
        //$this->Empresa = (!empty($data['Empresa'])) ? $data['Empresa'] : null;
        //--------------------------------------------------------------------------------------------------------------
        $this->CNombres = (!empty($data['CNombres'])) ? $data['CNombres'] : null;
        $this->CApellidos = (!empty($data['CApellidos'])) ? $data['CApellidos'] : null;
        $this->TipoDocumento = (!empty($data['TipoDocumento'])) ? $data['TipoDocumento'] : null;
        $this->TipoEmpresa = (!empty($data['TipoEmpresa'])) ? $data['TipoEmpresa'] : null;

        $this->checkboxLogoBeneficio = (!empty($data['checkboxLogoBeneficio'])) ? $data['checkboxLogoBeneficio'] : null;
        $this->checkboxLogo = (!empty($data['checkboxLogo'])) ? $data['checkboxLogo'] : null;

        $this->checkboxMoney = (!empty($data['checkboxMoney'])) ? $data['checkboxMoney'] : null;
        $this->checkboxTotalPuntos = (!empty($data['checkboxTotalPuntos'])) ? $data['checkboxTotalPuntos'] : null;


        $this->NombreTipoEmpresa = (!empty($data['NombreTipoEmpresa'])) ? $data['NombreTipoEmpresa'] : null;
        $this->TEliminado = (!empty($data['TEliminado'])) ? $data['TEliminado'] : null;

        $this->CantidadClientes = (!empty($data['CantidadClientes'])) ? $data['CantidadClientes'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function getInputFilter($tipoemp = array(), $usuario = array(), $limit = 8, $tipo = null, $option = null)
    {
        foreach ($tipoemp as $key => $dato) {
            $tipoemp_id[] = $key;
        }

        foreach ($usuario as $key => $dato) {
            $usuario_id[] = $key;
        }

        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            //*********** Datos Generales ***********//
            $inputFilter->add(
                array(
                    'name' => 'id',
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );
            if ($option == 2) {
                $inputFilter->add(
                    array(
                        'name' => 'TipoEmpresa',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
                    )
                );
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'TipoEmpresa',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
                    )
                );
            }
            $inputFilter->add(
                array(
                    'name' => 'ClaseEmpresaCliente',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'NombreComercial',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'El Nombre Comercial no de Tener mas de 255 caracteres.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'RazonSocial',
                    'required' => false,
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
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'ApellidoPaterno',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'ApellidoMaterno',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Nombre',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Ruc',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'min' => 11,
                                'max' => 11,
                                'messages' => array(
                                    'stringLengthTooShort' => 'El tamaño del Ruc es de 11 dígitos.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    'isEmpty' => 'El campo Ruc no puede quedar vacío.',
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
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 255,
                                'messages' => array(
                                    'stringLengthTooLong' => 'La Descripción supera el limite.',
                                )
                            )
                        )
                    )
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'IdSap',
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
                                    'stringLengthTooLong' => 'El idSap supera el limite permitido (45).',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'SubDominio',
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
                                    'stringLengthTooLong' => 'A superado el limite permitido (45).',
                                )
                            )
                        ),
                        array(
                            "name" => "Regex",
                            "options" => array(
                                "pattern" => "/(^[a-zA-Z]+[a-zA-Z0-9_\-\.]+[a-zA-Z0-9])$/",
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
                    'name' => 'SitioWeb',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'El Sitio Web supera el limite permitido (45).',
                                )
                            )
                        )
                    )
                )
            );

            //*********** Datos de Contacto ***********//
            $inputFilter->add(
                array(
                    'name' => 'RepresentanteLegal',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    )
                )
            );

            //número documento
            if ($tipo == 1) {
                $inputFilter->add(
                    array(
                        'name' => 'RepresentanteNumeroDocumento',
                        'required' => false,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => $limit,
                                    'max' => $limit,
                                    'messages' => array(
                                        'stringLengthInvalid' => 'El tamaño de la entrada es inválida',
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
            } else {
                $inputFilter->add(
                    array(
                        'name' => 'RepresentanteNumeroDocumento',
                        'required' => false,
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'max' => $limit,
                                    'messages' => array(
                                        'stringLengthInvalid' => 'El tamaño de la entrada es inválida',
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
                    'name' => 'BNF_TipoDocumento_id',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Tipo de Documento no puede quedar vacío.'
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'PaisLegal',
                    'required' => false
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DepartamentoLegal',
                    'required' => false
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'BNF_Ubigeo_id_legal',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Provincia Legal no puede quedar vacío.'
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DireccionLegal',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'La Direccion legal supera el limite permitido.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DireccionLegalDetalle',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'La Direccion legal supera el limite permitido.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'PaisEnvio',
                    'required' => false
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DepartamentoEnvio',
                    'required' => false
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'BNF_Ubigeo_id_envio',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Provincia Envio no puede quedar vacío.'
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DireccionEnvio',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'La Dirección de envio supera el limite.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DireccionEnvioDetalle',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'La Dirección de envio supera el limite.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'PaisEmpresa',
                    'required' => false
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DepartamentoEmpresa',
                    'required' => false
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'BNF_Ubigeo_id_empresa',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Provincia Empresa no puede quedar vacío.'
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DireccionEmpresa',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'La Dirección de Empresa supera el limite.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'DireccionEmpresaDetalle',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'La Dirección de Empresa supera el limite.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'HorarioAtencion',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'El tamaño de del Horario de Atención supera el limite.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'HorarioAtencionInicio',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'El tamaño de del Horario de Atención' .
                                        ' Inicio supera el limite.'
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'HorarioAtencionFin',
                    'required' => false,
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
                                    'stringLengthTooLong' =>
                                        'El tamaño del Horario de Atención Fin supera el limite.',
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
                    'filters' => array(
                        array('name' => 'Int'),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'checkboxTotalPuntos',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    )
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'checkboxLogoBeneficio',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    )
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'checkboxLogo',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    )
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'checkboxMoney',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    )
                )
            );




            $inputFilter->add(
                array(
                    'name' => 'Celular',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'PersonaAtencion',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'CargoPersonaAtencion',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'CorreoPersonaAtencion',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Email no puede quedar vacío.'
                            )
                        ),
                        array(
                            "name" => "Regex",
                            "options" => array(
                                "pattern" => "/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/",
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

            $inputFilter->add(
                array(
                    'name' => 'NombreContacto',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'TelefonoContacto',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'CorreoContacto',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Email no puede quedar vacío.'
                            )
                        ),
                        array(
                            "name" => "Regex",
                            "options" => array(
                                "pattern" => "/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/",
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

            $inputFilter->add(
                array(
                    'name' => 'HorarioAtencionContacto',
                    'required' => false,
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
                                    'stringLengthTooLong' =>
                                        'El tamaño de del Horario de Atención supera el limite.',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'HorarioAtencionInicioContacto',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'El tamaño de del Horario de Atención' .
                                        ' Inicio supera el limite.'
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'HorarioAtencionFinContacto',
                    'required' => false,
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
                                    'stringLengthTooLong' => 'El tamaño del Horario de Atención Fin supera el limite',
                                )
                            )
                        )
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Color',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 7,
                                'messages' => array(
                                    'stringLengthTooLong' => 'A superado el limite permitido (7).',
                                )
                            )
                        ),
                        array(
                            "name" => "Regex",
                            "options" => array(
                                "pattern" => "/^[#][a-zA-Z0-9]*$/",
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
                    'name' => 'Color_menu',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 7,
                                'messages' => array(
                                    'stringLengthTooLong' => 'A superado el limite permitido (7).',
                                )
                            )
                        ),
                        array(
                            "name" => "Regex",
                            "options" => array(
                                "pattern" => "/^[#][a-zA-Z0-9]*$/",
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
                    'name' => 'Color_hover',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'max' => 7,
                                'messages' => array(
                                    'stringLengthTooLong' => 'A superado el limite permitido (7).',
                                )
                            )
                        ),
                        array(
                            "name" => "Regex",
                            "options" => array(
                                "pattern" => "/^[#][a-zA-Z0-9]*$/",
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

            //*********** Otros ***********//
            $inputFilter->add(
                array(
                    'name' => 'BNF_Usuario_id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'No selecciono ningún usuario.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $usuario_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Usuario',
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

    public function getInputFilterSearch()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'RazonSocial',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'Int'),
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    )
                )
            );

            $inputFilter->add(
                array(
                    'name' => 'Ruc',
                    'required' => false,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim')
                    )
                )
            );

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
