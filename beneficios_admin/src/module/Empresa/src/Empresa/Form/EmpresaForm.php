<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 04/09/15
 * Time: 01:13 PM
 */

namespace Empresa\Form;

use Auth\Form\BaseForm;

class EmpresaForm extends BaseForm
{
    const EMPTY_OPTION = "Seleccione...";

    public function __construct($name = null, $tipo = array(), $usuario = array())
    {
        parent::__construct('buscar');
        $this->init($tipo,$usuario);
    }

    public function init( $tipo = array(), $usuario = array())
    {
        // we want to ignore the name passed

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(
            array(
                'name' => 'id',
                'type' => 'Hidden',
            )
        );

        //***************** Datos Generales ***************

        $this->add(
            array(
                'name' => 'TipoEmpresa',
                'type' => 'Zend\Form\Element\Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'multiple' => 'multiple',
                    'id' => 'TipoEmpresa',
                ),
                'options' => array(
                    'value_options' => $tipo,
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'ClaseEmpresaCliente',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'ClaseEmpresaCliente',
                    'disabled' => 'disabled'
                ),
                'options' => array(
                    'value_options' => array(
                        'Normal' => 'Normal',
                        'Especial' => 'Especial'
                    ),
                    'disable_inarray_validator' => true
                )
            )
        );

        $this->add(
            array(
                'name' => 'NombreComercial',
                'id' => 'NombreComercial',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );


        $this->add(
            array(
                'name' => 'checkboxTotalPuntos',
                'type' => 'Zend\Form\Element\Checkbox',
            )
        );
        $this->add(
            array(
                'name' => 'checkboxLogo',
                'type' => 'Zend\Form\Element\Checkbox',
            )
        );
        $this->add(
            array(
                'name' => 'checkboxLogoBeneficio',
                'type' => 'Zend\Form\Element\Checkbox',
            )
        );

        $this->add(
            array(
                'name' => 'checkboxMoney',
                'type' => 'Zend\Form\Element\Checkbox',
            )
        );





        $this->add(
            array(
                'name' => 'RazonSocial',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'ApellidoPaterno',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'ApellidoMaterno',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Nombre',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Ruc',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 11,
                    'placeholder' => 'Ruc',
                    'id' => 'ruc'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Descripcion',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'IdSap',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 45
                ),
            )
        );

        $this->add(
            array(
                'name' => 'SubDominio',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 45
                ),
            )
        );

        $this->add(
            array(
                'name' => 'SitioWeb',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 45
                ),
            )
        );

        //*************** Datos de Contacto *****************

        $this->add(
            array(
                'name' => 'RepresentanteLegal',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255,
                    'placeholder' => 'Representante Legal'
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'BNF_TipoDocumento_id',
                'options' => array(
                    'value_options' => array(
                        '0' => 'DNI',
                        '1' => 'Pasapoter'
                    ),
                    'empty_option' => $this::EMPTY_OPTION
                ),
                'attributes' => array(
                    'class' => 'form-control',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'RepresentanteNumeroDocumento',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                )
            )
        );

        //******************* Ubigeo ***************
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'PaisLegal',
                'attributes' => array(
                    'class' => 'pais form-control',
                    'id' => 'PaisLegal',
                ),
                'options' => array(
                    'empty_option' => $this::EMPTY_OPTION,
                    'disable_inarray_validator' => true
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'DepartamentoLegal',
                'attributes' => array(
                    'class' => 'depa form-control',
                    'id' => 'DepartamentoLegal',
                ),
                'options' => array(
                    'empty_option' => $this::EMPTY_OPTION,
                    'disable_inarray_validator' => true
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'BNF_Ubigeo_id_legal',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'ProvinciaLegal',
                ),
                'options' => array(
                    'empty_option' => $this::EMPTY_OPTION,
                    'disable_inarray_validator' => true
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'DireccionLegal',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'DireccionLegalDetalle',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'PaisEnvio',
                'attributes' => array(
                    'class' => 'pais form-control',
                    'id' => 'PaisEnvio',
                ),
                'options' => array(
                    'empty_option' => $this::EMPTY_OPTION,
                    'disable_inarray_validator' => true
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'DepartamentoEnvio',
                'attributes' => array(
                    'class' => 'depa form-control',
                    'id' => 'DepartamentoEnvio',
                ),
                'options' => array(
                    'empty_option' => $this::EMPTY_OPTION,
                    'disable_inarray_validator' => true
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'BNF_Ubigeo_id_envio',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'ProvinciaEnvio',
                ),
                'options' => array(
                    'empty_option' => $this::EMPTY_OPTION,
                    'disable_inarray_validator' => true
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'DireccionEnvio',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'DireccionEnvioDetalle',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'PaisEmpresa',
                'attributes' => array(
                    'class' => 'pais form-control',
                    'id' => 'PaisEmpresa',
                ),
                'options' => array(
                    'empty_option' => $this::EMPTY_OPTION,
                    'disable_inarray_validator' => true
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'DepartamentoEmpresa',
                'attributes' => array(
                    'class' => 'depa form-control',
                    'id' => 'DepartamentoEmpresa',
                ),
                'options' => array(
                    'empty_option' => $this::EMPTY_OPTION,
                    'disable_inarray_validator' => true
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'BNF_Ubigeo_id_empresa',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'ProvinciaEmpresa',
                ),
                'options' => array(
                    'empty_option' => $this::EMPTY_OPTION,
                    'disable_inarray_validator' => true
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'DireccionEmpresa',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'DireccionEmpresaDetalle',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );
        /* Fin de Ubigeo */

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'HoraAtencion',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'HoraAtencionInicio',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'HoraAtencionFin',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Telefono',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 9
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Celular',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 9
                ),
            )
        );

        $this->add(
            array(
                'name' => 'PersonaAtencion',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'CargoPersonaAtencion',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'CorreoPersonaAtencion',
                'type' => 'Zend\Form\Element\Email',
                'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'correo@example.com',
                    'maxLength' => 255
                ),
            )
        );

        //************ Campos de Contacto Cliente **************
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'HoraAtencionContacto',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'HoraAtencionInicioContacto',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'HoraAtencionFinContacto',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'NombreContacto',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 255
                ),
            )
        );

        $this->add(
            array(
                'name' => 'TelefonoContacto',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 9
                ),
            )
        );

        $this->add(
            array(
                'name' => 'CorreoContacto',
                'type' => 'Zend\Form\Element\Email',
                'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'correo@example.com',
                    'maxLength' => 255
                ),
            )
        );

        //************ Otros Campos **************
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'BNF_Usuario_id',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'value_options' => $usuario,
                    'empty_option' => $this::EMPTY_OPTION,
                )
            )
        );
        //************ Otros Campos **************
        $this->add(
            array(
                'name' => 'Color',
                'type' => 'Zend\Form\Element\Color',
                'attributes' => array(
                    'class' => 'form-control',
                    'value' => '#3c8dbc'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Color_menu',
                'type' => 'Zend\Form\Element\Color',
                'attributes' => array(
                    'class' => 'form-control',
                    'value' => '#0a0d12'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Color_hover',
                'type' => 'Zend\Form\Element\Color',
                'attributes' => array(
                    'class' => 'form-control',
                    'value' => '#400090'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Registrar',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }

    /**
     * Set a single option for an element
     *
     * @param  string $key
     * @param  mixed $value
     * @return self
     */
    public function setOption($key, $value)
    {
        // TODO: Implement setOption() method.
    }
}
