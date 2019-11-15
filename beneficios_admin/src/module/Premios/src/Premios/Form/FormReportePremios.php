<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 12/09/16
 * Time: 11:00
 */

namespace Premios\Form;

use Auth\Form\BaseForm;

class FormReportePremios extends BaseForm
{
    public function __construct($name = 'reporte', $value = array(), $type = null)
    {
        parent::__construct($name);
        $this->init($value, $type);
    }

    public function init($value = array(), $type = null)
    {
        $this->setAttribute('method', 'post');

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'checkboxDemo',
                'options' => array(
                    'label' => 'Demográfico',
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'checkboxComp',
                'options' => array(
                    'label' => 'Comportamiento',
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'checkboxPref',
                'options' => array(
                    'label' => 'Preferencia',
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'checkboxEmpresa',
                'options' => array(
                    'label' => 'Empresa Cliente',
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'checkboxCampania',
                'options' => array(
                    'label' => 'Campañas',
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'checkboxSegmento',
                'options' => array(
                    'label' => 'Segmentos',
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'checkboxUsuario',
                'options' => array(
                    'label' => 'Usuarios',
                    'use_hidden_element' => true,
                    'checked_value' => 1,
                    'unchecked_value' => 0
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        if($type == "cliente"){
            $this->add(
                array(
                    'name' => 'empresa',
                    'type' => 'Hidden',
                    'attributes' => array(
                        'id' => 'empresas'
                    ),
                )
            );
        }else{
            $this->add(
                array(
                    'name' => 'empresa',
                    'type' => 'Select',
                    'attributes' => array(
                        'class' => 'form-control select2',
                        'id' => 'empresas'
                    ),
                    'options' => array(
                        'value_options' => $value['emp'],
                        'empty_option' => 'Seleccione...'
                    )
                )
            );
        }

        $this->add(
            array(
                'name' => 'campania',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'campanias'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                )
            )
        );

        $this->add(
            array(
                'name' => 'segmento',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'segmentos'
                ),
                'options' => array(
                    'disable_inarray_validator' => true,
                )
            )
        );

        $this->add(
            array(
                'name' => 'usuario',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'placeholder' => 'Todos / DNI / Nombre / Apellidos'
                )
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Exportar',
                    'id' => 'addButton',
                    'class' => 'btn btn-primary',
                )
            )
        );
    }
}