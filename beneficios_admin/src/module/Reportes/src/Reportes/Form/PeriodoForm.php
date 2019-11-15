<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 30/12/15
 * Time: 05:26 PM
 */

namespace Reportes\Form;

use Auth\Form\BaseForm;

class PeriodoForm extends BaseForm
{
    public function __construct($name = 'periodo', $empresas = array(), $tipo = 0)
    {
        parent::__construct('periodo');
        $this->init($empresas, $tipo);
    }

    public function init($empresas = array(), $tipo = 0)
    {
        $this->add(
            array(
                'type' => 'Text',
                'name' => 'FechaInicio',
                'options' => array(
                    'format' => 'Y-m-d',
                ),
                'attributes' => array(
                    'class' => 'form-control datepicker',
                    'data-date-end-date' => '0d',
                )
            )
        );

        $this->add(
            array(
                'type' => 'Text',
                'name' => 'FechaFin',
                'options' => array(
                    'format' => 'Y-m-d',
                ),
                'attributes' => array(
                    'class' => 'form-control datepicker',
                    'data-date-end-date' => '0d',
                )
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Exportar',
                    'id' => 'submitbutton',
                    'class' => 'btn btn-primary',
                ),
            )
        );

        if ($tipo == 7) {
            $this->add(
                array(
                    'type' => 'Zend\Form\Element\Hidden',
                    'name' => 'empresa',
                    'attributes' => array(
                        'id' => 'empresa-search',
                        'value' => $empresas
                    )
                )
            );
        } else {
            $this->add(
                array(
                    'name' => 'empresa',
                    'type' => 'Select',
                    'attributes' => array(
                        'id' => 'empresa-search',
                        'class' => 'select2',
                    ),
                    'options' => array(
                        'value_options' => $empresas,
                        'empty_option' => 'Listar Todos'
                    )
                )
            );
        }

        $this->add(
            array(
                'name' => 'Costo',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'costo',
                    'class' => 'form-control',
                )
            )
        );

        $this->add(
            array(
                'name' => 'Meta',
                'type' => 'Text',
                'attributes' => array(
                    'id' => 'meta',
                    'class' => 'form-control',
                )
            )
        );

        $this->add(
            array(
                'type' => 'Text',
                'name' => 'FechaInicio2',
                'options' => array(
                    'format' => 'Y-m-d',
                ),
                'attributes' => array(
                    'class' => 'form-control datepicker',
                    'data-date-end-date' => '0d',
                )
            )
        );

        $this->add(
            array(
                'type' => 'Text',
                'name' => 'FechaFin2',
                'options' => array(
                    'format' => 'Y-m-d',
                ),
                'attributes' => array(
                    'class' => 'form-control datepicker',
                    'data-date-end-date' => '0d',
                )
            )
        );

        $this->add(
            array(
                'type' => 'Text',
                'name' => 'Emails',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'emails'
                )
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