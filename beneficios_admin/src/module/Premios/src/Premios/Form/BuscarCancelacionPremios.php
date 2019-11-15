<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 21/07/16
 * Time: 04:55 PM
 */

namespace Premios\Form;

use Auth\Form\BaseForm;

class BuscarCancelacionPremios extends BaseForm
{
    public function __construct($name = null, $value = array())
    {
        parent::__construct('buscar-usuarios');
        $this->init($value);
    }

    public function init($value = array())
    {
        $this->add(
            array(
                'name' => 'Empresas',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'empresa-cli',
                ),
                'options' => array(
                    'value_options' => $value['empresa'],
                    'disable_inarray_validator' => true,
                    'empty_option' => 'Listar Todos'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Campania',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'campanias',
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Segmento',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'segmentos',
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Cliente',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'nombreCliente',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Buscar',
                    'id' => 'submitButton',
                    'class' => 'btn btn-primary',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Estado',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'estado',
                ),
                'options' => array(
                    'value_options' => array(
                        'Activado' => 'Activos',
                        'Desactivado' => 'Inactivos',
                        'Cancelado' => 'Eliminados',
                    ),
                    'disable_inarray_validator' => true,
                ),
            )
        );
    }
}