<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 11/09/15
 * Time: 11:19 AM
 */

namespace Categoria\Form;

use Auth\Form\BaseForm;


class CategoriaForm extends BaseForm
{
    public function __construct($name = null, $value = array())
    {
        parent::__construct('categoria');
        $this->init($value);
    }

    public function init($value = array())
    {
        // we want to ignore the name passed

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
                'name' => 'Nombre',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control md',
                    'maxLength' => 255
                ),
            )
        );
        $this->add(
            array(
                'name' => 'NombrePais',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'pais'
                ),
                'options' => array(
                    'value_options' => $value,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Registrar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Descripcion',
                'attributes' => array(
                    'type' => 'textarea',
                    'class' => 'form-control md',
                    'rows' => 10,
                    'cols' => 10,
                    'maxLength' => 255
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
