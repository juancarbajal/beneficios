<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 22/06/16
 * Time: 11:15 AM
 */

namespace Demanda\Form;

use Auth\Form\BaseForm;

class FormDemanda extends BaseForm
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
                'name' => 'action',
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
                'type' => 'Zend\Form\Element\Text',
                'name' => 'FechaDemanda',
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
                    'id' => 'campanias'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Segmento',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control searchable',
                    'multiple' => 'multiple',
                    'id' => 'segmentos'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Rubros',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'rubros',
                    'multiple' => 'multiple'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                    'value_options' => $value['rubro']
                ),
            )
        );

        $this->add(
            array(
                'name' => 'EmpresaProveedor',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control searchable',
                    'multiple' => 'multiple',
                    'id' => 'empresa-prov'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                    'value_options' => $value['empprov']
                ),
            )
        );

        $this->add(
            array(
                'name' => 'EmpresasAdicionales',
                'type' => 'Zend\Form\Element\Textarea',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Departamentos',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control searchable',
                    'multiple' => 'multiple',
                    'id' => 'departamentos'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                    'value_options' => $value['depas']
                ),
            )
        );

        $this->add(
            array(
                'name' => 'PrecioMin',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'PrecioMax',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Target',
                'type' => 'Zend\Form\Element\Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Comentarios',
                'type' => 'Zend\Form\Element\Textarea',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Actualizaciones',
                'type' => 'Zend\Form\Element\Textarea',
                'attributes' => array(
                    'class' => 'form-control textarea'
                )
            )
        );

        $this->add(
            array(
                'name' => 'send',
                'type' => 'button',
                'attributes' => array(
                    'value' => 'Enviar',
                    'id' => 'sendButton',
                    'class' => 'btn btn-default',
                )
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Guardar',
                    'id' => 'submitSave',
                    'class' => 'btn btn-primary',
                ),
            )
        );
    }
}
