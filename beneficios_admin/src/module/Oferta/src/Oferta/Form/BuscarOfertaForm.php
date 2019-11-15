<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 15/09/15
 * Time: 11:02 AM
 */

namespace Oferta\Form;

use Auth\Form\BaseForm;


class BuscarOfertaForm extends BaseForm
{
    public function __construct($name = 'buscar', $value = array())
    {
        parent::__construct('buscar');
        $this->init($value);
    }

    public function init( $value = array())
    {
        // we want to ignore the name passed
        $this->add(
            array(
                'name' => 'Empresa',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $value['emp'],
                    'empty_option' => 'Listar Todos'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Tipo',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $value['tip'],
                    'empty_option' => 'Listar Todos'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Rubro',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $value['rub'],
                    'empty_option' => 'Listar Todos'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Categoria',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $value['cat'],
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
                ),
                'options' => array(
                    'value_options' => $value['cam'],
                    'empty_option' => 'Listar Todos'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Nombre',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $value['nom'],
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
