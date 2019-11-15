<?php

namespace Cliente\Form;

use Auth\Form\BaseForm;
use Zend\Form\Element;

class FinalUserForm extends BaseForm
{

    public function __construct($tipo = array(), $name = null)
    {
        parent::__construct('cliente');
        $this->init($tipo);
    }

    public function init($tipo = array())
    {

        $this->add(
            array(
                'name' => 'id',
                'type' => 'Hidden',
            )
        );

        $this->add(
            array(
                'name' => 'Nombre',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 100
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Apellido',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 100
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Radio',
                'name' => 'Genero',
                'options' => array(
                    'value_options' => array(
                        'H' => 'Hombre',
                        'M' => 'Mujer',
                    ),
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Text',
                'name' => 'FechaNacimiento',
                'attributes' => array(
                    'class' => 'form-control date-picker',
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Select',
                'name' => 'BNF_TipoDocumento_id',
                'options' => array(
                    'value_options' => $tipo,
                    'empty_option' => 'Seleccione...'
                ),
                'attributes' => array(
                    'class' => 'form-control',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'NumeroDocumento',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 15
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'Eliminado',
                'options' => array(
                    'checked_value' => 0,
                    'unchecked_value' => 1
                ),
                'attributes' => array(
                    'value' => 0
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Submit',
                'name' => 'submit',
                'attributes' => array(
                    'value' => 'Registrar',
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
