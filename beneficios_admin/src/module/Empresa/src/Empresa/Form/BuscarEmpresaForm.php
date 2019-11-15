<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 01/09/15
 * Time: 06:44 PM
 */

namespace Empresa\Form;

use Auth\Form\BaseForm;


class BuscarEmpresaForm extends BaseForm
{

    public function __construct($name = null, $value = array())
    {
        parent::__construct('buscar');
        $this->init($value);
    }

    public function init($value = array())
    {
        // we want to ignore the name passed


        $this->add(
            array(
                'name' => 'RazonSocial',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $value,
                    'disable_inarray_validator' => true,
                    'empty_option' => 'Listar Todos'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Ruc',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control',
                    'maxLength' => 11
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Buscar',
                    'id' => 'submitbutton',
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
