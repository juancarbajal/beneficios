<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 26/08/15
 * Time: 18:31 AM
 */

namespace Usuario\Form;

use Auth\Form\BaseForm;


class UsuarioForm extends BaseForm
{

    public function __construct($datos = array(), $docs = array(), $empresa = array(), $name = null)
    {
        parent::__construct('usuario');
        $this->init($datos, $docs, $empresa);
    }

    public function init($datos = array(), $docs = array(), $empresa = array())
    {
        // we want to ignore the name passed

        parent::__construct('usuario');

        $this->add(
            array(
                'name' => 'id',
                'type' => 'Hidden',
            )
        );

        $this->add(
            array(
                'name' => 'BNF_TipoUsuario_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'tipusu',
                ),
                'options' => array(
                    'value_options' => $datos,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'BNF_Empresa_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'empresa',
                ),
                'options' => array(
                    'value_options' => $empresa,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Nombres',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Apellidos',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control',
                ),
            )
        );

        $this->add(
            array(
                'type' => 'Text',
                'name' => 'Correo',
                'attributes' => array(
                    'class' => 'form-control',
                ),
            )
        );
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Password',
                'name' => 'Contrasenia',
                'attributes' => array(
                    'class' => 'form-control',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'BNF_TipoDocumento_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'value_options' => $docs,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'NumeroDocumento',
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
                    'value' => 'Registrar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );

        $this->setUseInputFilterDefaults(false);
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
