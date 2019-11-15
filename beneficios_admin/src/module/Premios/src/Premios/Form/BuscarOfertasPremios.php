<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 05/07/16
 * Time: 12:48 AM
 */

namespace Premios\Form;

use Auth\Form\BaseForm;

class BuscarOfertasPremios extends BaseForm
{
    public function __construct($name = null, $value = array())
    {
        parent::__construct('buscar ofertas');
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
                ),
                'options' => array(
                    'value_options' => $value['emp'],
                    'disable_inarray_validator' => true,
                    'empty_option' => 'Listar Todos'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Ofertas',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $value['ofertas'],
                    'disable_inarray_validator' => true,
                    'empty_option' => 'Listar Todos'
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
    }
}
