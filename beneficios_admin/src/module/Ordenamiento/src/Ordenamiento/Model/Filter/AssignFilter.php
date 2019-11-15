<?php
/**
 * Created by PhpStorm.
 * User: marlo
 * Date: 01/10/15
 * Time: 05:34 PM
 */

namespace Ordenamiento\Model\Filter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilterAwareInterface;

class AssignFilter
{
    protected $inputFilter;

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter(
        $categoria = array(),
        $campania = array(),
        $layout = array(),
        $ofertas = array(),
        $empresas = array(),
        $tipo = null
    ) {
        if ($campania == array()) {
            $campania_id[0] = '0';
        }
        if ($categoria == array()) {
            $categoria_id[0] = '0';
        }
        if ($layout == array()) {
            $layout_id[0] = '0';
        }
        if ($ofertas == array()) {
            $ofertas_id[0] = '0';
        }
        if ($empresas == array()) {
            $empresas_id[0] = '0';
        }
        foreach ($campania as $key => $dato) {
            $campania_id[] = $key;
        }
        foreach ($categoria as $key => $dato) {
            $categoria_id[] = $key;
        }
        foreach ($layout as $key => $dato) {
            $layout_id[] = $key;
        }
        foreach ($ofertas as $key => $dato) {
            $ofertas_id[] = $key;
        }
        foreach ($empresas as $key => $dato) {
            $empresas_id[] = $key;
        }
        //var_dump($campania_id);exit;
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(
                array(
                    'name' => 'id',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Int'),
                    ),
                )
            );

            if ($tipo == 'categoria') {
                //Categoria (select)
                $inputFilter->add(
                    array(
                        'name' => 'BNF_Categoria_id',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Categoria no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $categoria_id,
                                    'messages' => array(
                                        'notInArray' => 'Seleccione una Categoría',
                                    )
                                )
                            )
                        ),
                    )
                );
                //Campaña (select)
                $inputFilter->add(
                    array(
                        'name' => 'BNF_Campanias_id',
                        'required' => false,
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Campaña no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $campania_id,
                                    'messages' => array(
                                        'notInArray' => 'Seleccione una Campaña',
                                    )
                                )
                            )
                        ),
                    )
                );
            } elseif ($tipo == 'campania') {
                //Categoria (select)
                $inputFilter->add(
                    array(
                        'name' => 'BNF_Categoria_id',
                        'required' => false,
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Categoria no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $categoria_id,
                                    'messages' => array(
                                        'notInArray' => 'Seleccione una Categoría',
                                    )
                                )
                            )
                        ),
                    )
                );
                //Campaña (select)
                $inputFilter->add(
                    array(
                        'name' => 'BNF_Campanias_id',
                        'required' => true,
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Campaña no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $campania_id,
                                    'messages' => array(
                                        'notInArray' => 'Seleccione una Campaña',
                                    )
                                )
                            )
                        ),
                    )
                );
            } else {
                //Categoria (select)
                $inputFilter->add(
                    array(
                        'name' => 'BNF_Categoria_id',
                        'required' => false,
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Categoria no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $categoria_id,
                                    'messages' => array(
                                        'notInArray' => 'Seleccione una Categoría',
                                    )
                                )
                            )
                        ),
                    )
                );
                //Campaña (select)
                $inputFilter->add(
                    array(
                        'name' => 'BNF_Campanias_id',
                        'required' => false,
                        'validators' => array(
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'message' => 'El campo Campaña no puede quedar vacío.'
                                )
                            ),
                            array(
                                'name' => 'InArray',
                                'options' => array(
                                    'haystack' => $campania_id,
                                    'messages' => array(
                                        'notInArray' => 'Seleccione una Campaña',
                                    )
                                )
                            )
                        ),
                    )
                );
            }
            //Fila 1 (select)
            $inputFilter->add(
                array(
                    'name' => 'Fila_1',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Fila 1 no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $layout_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Layout',
                                )
                            )
                        )
                    ),
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'Fila_1_1',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $ofertas_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Oferta Valida',
                                )
                            )
                        )
                    )
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'Fila_1_2',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $ofertas_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Oferta Valida',
                                )
                            )
                        )
                    )
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'Fila_1_3',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $ofertas_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Oferta Valida',
                                )
                            )
                        )
                    )
                )
            );
            //Fila 2 (select)
            $inputFilter->add(
                array(
                    'name' => 'Fila_2',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Fila 2 no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $layout_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Layout',
                                )
                            )
                        )
                    ),
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'Fila_2_1',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $ofertas_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Oferta Valida',
                                )
                            )
                        )
                    )
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'Fila_2_2',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $ofertas_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Oferta Valida',
                                )
                            )
                        )
                    )
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'Fila_2_3',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $ofertas_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Oferta Valida',
                                )
                            )
                        )
                    )
                )
            );
            //Fila 3 (select)
            $inputFilter->add(
                array(
                    'name' => 'Fila_3',
                    'required' => true,
                    'validators' => array(
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'message' => 'El campo Fila 3 no puede quedar vacío.'
                            )
                        ),
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $layout_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione un Layout',
                                )
                            )
                        )
                    ),
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'Fila_3_1',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $ofertas_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Oferta Valida',
                                )
                            )
                        )
                    )
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'Fila_3_2',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $ofertas_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Oferta Valida',
                                )
                            )
                        )
                    )
                )
            );
            $inputFilter->add(
                array(
                    'name' => 'Fila_3_3',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $ofertas_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Oferta Valida',
                                )
                            )
                        )
                    )
                )
            );
            //Empresa
            $inputFilter->add(
                array(
                    'name' => 'empresa',
                    'required' => false,
                    'validators' => array(
                        array(
                            'name' => 'InArray',
                            'options' => array(
                                'haystack' => $empresas_id,
                                'messages' => array(
                                    'notInArray' => 'Seleccione una Empresa Valida',
                                )
                            )
                        )
                    )
                )
            );


            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}
