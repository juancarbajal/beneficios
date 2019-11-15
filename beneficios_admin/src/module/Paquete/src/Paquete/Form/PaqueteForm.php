<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 02/09/15
 * Time: 04:55 PM
 */

namespace Paquete\Form;

use Auth\Form\BaseForm;


class PaqueteForm extends BaseForm
{

    public function __construct($pais = array(), $tipo = array(), $name = null)
    {
        parent::__construct('paquete');
        $this->init($pais, $tipo);
    }

    public function init( $pais = array(), $tipo = array() )
    {
        // we want to ignore the name passed


        $this->add(
            array(
                'name' => 'id',
                'type' => 'Hidden',
            )
        );
        $this->add(
            array(
                'name' => 'NombrePais',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control _mg',
                ),
                'options' => array(
                    'value_options' => $pais,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Nombre',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control input-md',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'BNF_TipoPaquete_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'tipopaq',
                ),
                'options' => array(
                    'value_options' => $tipo,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'Precio',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control input-md lead',
                    'placeholder' => 'S/.00'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'CantidadDescargas',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control des input-md',
                )
            )
        );
        $this->add(
            array(
                'name' => 'PrecioUnitarioDescarga',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control des input-md',
                    'placeholder' => 'S/.00'

                ),
            )
        );
        $this->add(
            array(
                'name' => 'Bonificacion',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control des input-md',
                )
            )
        );
        $this->add(
            array(
                'name' => 'PrecioUnitarioBonificacion',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control des input-md',
                    'placeholder' => 'S/.00'
                ),
            )
        );
        $this->add(
            array(
                'name' => 'NumeroDias',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control pre input-md',
                )
            )
        );
        $this->add(
            array(
                'name' => 'CostoDia',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control pre input-md',
                    'placeholder' => 'S/.00'
                ),
            )
        );
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'Eliminado',
                'options' => array(
                    'use_hidden_element' => true,
                    'checked_value' => '0',
                    'unchecked_value' => '1'
                ),
                'attributes' => array(
                    'value' => '0',
                    'class' => 'check'
                )
            )
        );
        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Registrar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary mg-b',
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
