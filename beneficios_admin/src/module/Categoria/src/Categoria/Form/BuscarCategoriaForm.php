<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 10/09/15
 * Time: 04:02 PM
 */

namespace Categoria\Form;

use Auth\Form\BaseForm;


class BuscarCategoriaForm extends BaseForm
{

    public function __construct($name = 'buscar', $value = array())
    {
        parent::__construct('buscar');
        $this->init($value);
    }

    public function init($value = array())
    {
        // we want to ignore the name passed


        $this->add(
            array(
                'name' => 'Pais',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                ),
                'options' => array(
                    'value_options' => $value['pais'],
                    'empty_option' => 'Listar Todos'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Nombre',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control',
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
