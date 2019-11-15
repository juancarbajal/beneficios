<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 05/09/15
 * Time: 01:03 PM
 */

namespace Paquete\Form;

use Auth\Form\BaseForm;


class AsignarForm extends BaseForm
{
    public function __construct($pais = array(), $empresa = array(), $asesor = array(), $name = null)
    {
        parent::__construct('paquete');
        $this->init($pais,$empresa,$asesor);
    }

    public function init($pais = array(), $empresa = array(), $asesor = array())
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
                    'id' => 'pais',
                ),
                'options' => array(
                    'value_options' => $pais,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'BNF_Empresa_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control input-md select2',
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
                'name' => 'BNF_TipoPaquete_id',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control',
                    'id' => 'tipopaq',
                ),
            )
        );
        $this->add(
            array(
                'name' => 'BNF_Paquete_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'paquete',
                ),
                'options' => array(
                    'empty_option' => 'Seleccione...',
                    'disable_inarray_validator' => true
                ),
            )
        );
        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'FechaCompra',
                'attributes' => array(
                    'class' => 'form-control datepicker',
                    'data-date-end-date' => '0d',
                )
            )
        );
        $this->add(
            array(
                'name' => 'Factura',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control input-md',
                )
            )
        );
        $this->add(
            array(
                'name' => 'BNF_Usuario_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'tipopaq',
                ),
                'options' => array(
                    'value_options' => $asesor,
                    'empty_option' => 'Seleccione...',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'MaximoLeads',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control input-md lead',
                )
            )
        );

        $this->add(
            array(
                'name' => 'CostoPorLead',
                'attributes' => array(
                    'type' => 'Text',
                    'class' => 'form-control input-md lead',
                    'placeholder' => 'S/.00',
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
