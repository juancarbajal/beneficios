<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 22/06/16
 * Time: 11:15 AM
 */

namespace Demanda\Form;

use Auth\Form\BaseForm;

class BuscarDemandasForm extends BaseForm
{
    public function __construct($name = 'agregar', $value = array())
    {
        parent::__construct('agregar');
        $this->init($value);
    }

    public function init($value = array())
    {
        $this->setAttribute('method', 'post');

        $this->add(
            array(
                'name' => 'id',
                'attributes' => array(
                    'type' => 'hidden',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'EmpresaCliente',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'empresa-cli'
                ),
                'options' => array(
                    'value_options' => $value['empcli'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'FechaDemanda',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control datepicker'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Campania',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'campania'
                ),
                'options' => array(
                    'value_options' => $value['campania'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'buscar',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Buscar',
                    'id' => 'searchButton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
