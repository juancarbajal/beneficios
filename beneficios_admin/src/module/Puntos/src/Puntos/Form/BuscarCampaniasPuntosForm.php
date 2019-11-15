<?php
/**
 * Created by PhpStorm.
 * User: janaq
 * Date: 17/06/16
 * Time: 12:13 PM
 */

namespace Puntos\Form;

use Auth\Form\BaseForm;

class BuscarCampaniasPuntosForm extends BaseForm
{
    public function __construct($name = null, $value = array())
    {
        parent::__construct('buscar campanias');
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
                'type' => 'Zend\Form\Element\Text',
                'name' => 'FechaCampania',
                'attributes' => array(
                    'class' => 'form-control datepicker'
                )
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
