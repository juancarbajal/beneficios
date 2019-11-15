<?php

namespace Referido\Form;

use Auth\Form\BaseForm;
use Zend\Form\Element;

class ConfiguracionRefForm extends BaseForm
{

    public function __construct($tipo = array(), $name = null)
    {
        parent::__construct('cliente');
        $this->init($tipo);
    }

    public function init($tipo = array())
    {
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(
            array(
                'name' => 'id',
                'type' => Element\Hidden::class,
            )
        );

        $this->add(
            array(
                'name' => 'repeticion_01',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'repeticion-01',
                    'class' => 'form-control',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'repeticion_02',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'repeticion-02',
                    'class' => 'form-control',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'repeticion_03',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'repeticion-03',
                    'class' => 'form-control',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'repeticion_04',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'repeticion-04',
                    'class' => 'form-control',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'correo_ref',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'correo_ref',
                    'class' => 'form-control',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'banner_ref',
                'type' => 'file',
                'attributes' => array(
                    'class' => 'form-control btn-flat btn-upload',
                    'id' => 'banner_ref',
                )
            )
        );

        $this->add(
            array(
                'name' => 'banner_link_ref',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'banner_link_ref',
                    'class' => 'form-control',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'popup_ref',
                'type' => 'file',
                'attributes' => array(
                    'class' => 'form-control btn-flat btn-upload',
                    'id' => 'popup_ref',
                )
            )
        );

        $this->add(
            array(
                'type' => Element\Submit::class,
                'name' => 'submit',
                'attributes' => array(
                    'value' => 'Guardar',
                    'id' => 'submit-button',
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
