<?php
/**
 * Created by PhpStorm.
 * User: luisvar
 * Date: 15/09/15
 * Time: 10:45 AM
 */

namespace Oferta\Form;

use Auth\Form\BaseForm;
use Zend\Form\Element;

class OfertaForm extends BaseForm
{

    public function __construct($name = 'registrar', $value = array())
    {
        parent::__construct($name);
        $this->init($value);
    }

    public function init($value = array())
    {
        // we want to ignore the name passed

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');

        $this->add(
            array(
                'name' => 'id',
                'attributes' => array(
                    'type' => 'hidden',
                    'id' => 'oferta_id'
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

        //************* Datos de Asignacion *************//
        $this->add(
            array(
                'name' => 'Empresa',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control select2',
                    'id' => 'empresa-prov'
                ),
                'options' => array(
                    'value_options' => $value['emp'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Descarga',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'disabled' => 'disabled'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Presencia',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'disabled' => 'disabled'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Lead',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'disabled' => 'disabled'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Tipo',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'tipo',
                ),
                'options' => array(
                    'value_options' => $value['tip'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Stock',
                'type' => 'Number',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'stock',
                    'step' => '1',
                )
            )
        );

        $this->add(
            array(
                'name' => 'StockInicial',
                'type' => 'Hidden',
                'attributes' => array(
                    'id' => 'stockinicial',
                )
            )
        );

        //************* Datos Generales *************//
        $this->add(
            array(
                'name' => 'Nombre',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Titulo',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'TituloCorto',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'SubTitulo',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'BNF_TipoBeneficio_id',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'BNF_TipoBeneficio_id',
                ),
                'options' => array(
                    'value_options' => $value['tib'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'DatoBeneficio',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'DatoBeneficio',
                )
            )
        );

        $this->add(
            array(
                'name' => 'input-file',
                'attributes' => array(
                    'id' => 'input-file',
                    'type' => 'file',
                    'class' => 'form-control btn btn-flat btn-upload',
                    'maxLength' => 255,
                    //'multiple' => 'multiple',
                    "style" => "text-align: center; vertical-align: middle;"
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Descripcion',
                'type' => 'Textarea',
                'attributes' => array(
                    'class' => 'form-control textarea',
                    'style' => "width: 100%; height: 200px;"
                )
            )
        );

        $this->add(
            array(
                'name' => 'CondicionesUso',
                'type' => 'Textarea',
                'attributes' => array(
                    'class' => 'form-control textarea',
                    'style' => "width: 100%; height: 200px"
                )
            )
        );

        $this->add(
            array(
                'name' => 'Direccion',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Telefono',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control',
                    'maxLength' => 50
                )
            )
        );

        $this->add(
            array(
                'name' => 'Premium',
                'type' => 'Checkbox',
                'options' => array(
                    'checked_value' => 1,
                    'unchecked_value' => 0
                ),
                'attributes' => array(
                    'value' => 0,
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'name' => 'Correo',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'CorreoContacto',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Rubro',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'value_options' => $value['rub'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        //************* Ubicacion *************//

        $this->add(
            array(
                'name' => 'Pais',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                    'id' => 'pais'
                ),
                'options' => array(
                    'value_options' => $value['pais'],
                    'empty_option' => 'Seleccione...'
                ),
            )
        );

        $this->add(
            array(
                'name' => 'Distrito',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\MultiCheckbox',
                'name' => 'Departamento',
                'options' => array(
                    'use_hidden_element' => true,
                    'value_options' => $value['ubig']
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\MultiCheckbox',
                'name' => 'Categoria',
                'options' => array(
                    'use_hidden_element' => true,
                    'value_options' => $value['cat']
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\MultiCheckbox',
                'name' => 'Campania',
                'options' => array(
                    'use_hidden_element' => true,
                    'value_options' => $value['cam']
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        //************* Datos de Publicacion *************//

        $this->add(
            array(
                'type' => 'Zend\Form\Element\MultiCheckbox',
                'name' => 'Segmento',
                'options' => array(
                    'use_hidden_element' => true,
                    'value_options' => $value['seg']
                ),
                'attributes' => array(
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;"
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'FechaFinVigencia',
                'attributes' => array(
                    'class' => 'form-control datepicker'
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'FechaInicioPublicacion',
                'attributes' => array(
                    'class' => 'form-control datepicker',
                    'id' => 'datepicker'
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Text',
                'name' => 'FechaFinPublicacion',
                'attributes' => array(
                    'class' => 'form-control datepicker'
                )
            )
        );

        $this->add(
            array(
                'name' => 'DescargaMaximaDia',
                'type' => 'Text',
                'attributes' => array(
                    'class' => 'form-control'
                )
            )
        );

        $this->add(
            array(
                'name' => 'Estado',
                'type' => 'Select',
                'attributes' => array(
                    'class' => 'form-control',
                ),
                'options' => array(
                    'value_options' => array(
                        'Pendiente' => 'Pendiente',
                        'Publicado' => 'Publicado',
                        'Caducado' => 'Caducado'
                    ),
                    'empty_option' => 'Seleccione...'
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
                    'value' => 0,
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Textarea',
                'name' => 'textobanner',
                'attributes' => array(
                    'class' => 'form-control',
                    'rows' => 3
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Textarea',
                'name' => 'CondicionesTebca',
                'attributes' => array(
                    'class' => 'form-control textarea',
                    'style' => "width: 100%; height: 200px",
                    'rows' => 3
                )
            )
        );

        $this->add(
            array(
                'type' => 'Zend\Form\Element\Checkbox',
                'name' => 'TipoAtributo',
                'options' => array(
                    'label' => 'Split',
                    'use_hidden_element' => true,
                    'checked_value' => "Split",
                    'unchecked_value' => ""
                ),
                'attributes' => array(
                    'value' => "",
                    'class' => 'checkbox-inline',
                    'style' => "margin-left: 2em;",
                    'id' => 'tipoOferta',
                )
            )
        );

        $this->add(
            array(
                'name' => 'copy',
                'type' => 'submit',
                'attributes' => array(
                    'value' => 'Grabar de nuevo',
                    'id' => 'copyButton',
                    'class' => 'btn btn-default',
                ),
            )
        );

        $this->add(
            array(
                'name' => 'submit',
                'type' => 'Submit',
                'attributes' => array(
                    'value' => 'Registrar',
                    'id' => 'submitButton',
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
