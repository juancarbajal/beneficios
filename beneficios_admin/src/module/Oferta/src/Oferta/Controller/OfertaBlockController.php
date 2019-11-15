<?php

namespace Oferta\Controller;

use Oferta\Form\OfertaBlockForm;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Oferta\Model\OfertaEmpresaCliente;
use Oferta\Model\OfertaSubgrupo;

class OfertaBlockController extends AbstractActionController
{
    #region ObjectTable
    public function getOfertaTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaTable');
    }

    public function getEmpresaTable()
    {
        return $this->serviceLocator->get('Empresa\Model\EmpresaTable');
    }

    public function getOfertaSubgrupoTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaSubgrupoTable');
    }

    public function getSubgrupoTable()
    {
        return $this->serviceLocator->get('Usuario\Model\SubGrupoTable');
    }

    public function getOfertaEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Oferta\Model\Table\OfertaEmpresaClienteTable');
    }

    #endregion

    public function indexAction()
    {
        $mensaje = null;
        $alert = null;

        $identity = $this->identity();
        if (!$identity) {
            return $this->redirect()->toUrl('/login');
        }

        $form = new OfertaBlockForm();
        $ofertas = $this->getOfertaTable()->getOfertasTitulo();
        $empresas = $this->getEmpresaTable()->getEmpresasCliente();

        $request = $this->getRequest();
        if ($request->isPost()) {
            if (isset($request->getPost()->oferta)) {
                if (isset($request->getPost()->empresa)) {
                    //Recorremos las ofertas
                    foreach ($request->getPost()->oferta as $oferta) {
                        foreach ($request->getPost()->empresa as $empresa) {
                            //Verfica que la Empresa Normal Exista
                            if ($this->getEmpresaTable()->getEmpresasClienteNormExist($empresa)) {
                                //Verificamos si Tiene Relacion de Oferta Empresa Cliente
                                if ($this->getOfertaEmpresaClienteTable()
                                        ->getOfertaEmpresaClienteExist($oferta, $empresa) > 0
                                ) {
                                    $ofertaEmpCli = $this->getOfertaEmpresaClienteTable()
                                        ->getOfertaEmpresaClienteSeach($oferta, $empresa);
                                    $ofertaEmpCli->Eliminado = '0';
                                    $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                                } else {
                                    $ofertaEmpCli = new OfertaEmpresaCliente();
                                    $ofertaEmpCli->BNF_Empresa_id = $empresa;
                                    $ofertaEmpCli->BNF_Oferta_id = $oferta;
                                    $ofertaEmpCli->Eliminado = '0';
                                    $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                                }
                            } //Verfica que la Empresa Especial Exista
                            elseif ($this->getEmpresaTable()->getEmpresasClienteEspExist($empresa)) {
                                //Verificamos si Tiene Relacion de Oferta Empresa Cliente
                                if ($this->getOfertaEmpresaClienteTable()
                                        ->getOfertaEmpresaClienteExist($oferta, $empresa) > 0
                                ) {
                                    $ofertaEmpCli = $this->getOfertaEmpresaClienteTable()
                                        ->getOfertaEmpresaClienteSeach($oferta, $empresa);
                                    $ofertaEmpCli->Eliminado = '0';
                                    $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                                } else {
                                    $ofertaEmpCli = new OfertaEmpresaCliente();
                                    $ofertaEmpCli->BNF_Empresa_id = $empresa;
                                    $ofertaEmpCli->BNF_Oferta_id = $oferta;
                                    $ofertaEmpCli->Eliminado = '0';
                                    $this->getOfertaEmpresaClienteTable()->saveOfertaEmpresaCliente($ofertaEmpCli);
                                }
                                //Verificamos Relacion de Subgrupos
                                $subgrupos = $this->getSubgrupoTable()->getSubgruposEmpresa($empresa);
                                foreach ($subgrupos as $subgrupo) {
                                    //Verificamos si Tiene Relacion de Oferta Subgrupo
                                    if ($this->getOfertaSubgrupoTable()->getOfertaSubgrupoExist($oferta, $subgrupo->id) > 0) {
                                        $ofertaSubgrupo = $this->getOfertaSubgrupoTable()
                                            ->getOfertaSubgrupoSeach($oferta, $subgrupo->id);
                                        $ofertaSubgrupo->Eliminado = '0';
                                        $this->getOfertaSubgrupoTable()->saveOfertaSubgrupo($ofertaSubgrupo);
                                    } else {
                                        $ofertaSubgrupo = new OfertaSubgrupo();
                                        $ofertaSubgrupo->BNF_Subgrupo_id = $subgrupo->id;
                                        $ofertaSubgrupo->BNF_Oferta_id = $oferta;
                                        $ofertaSubgrupo->Eliminado = '0';
                                        $this->getOfertaSubgrupoTable()->saveOfertaSubgrupo($ofertaSubgrupo);
                                    }
                                }
                            }
                        }
                    }
                    $alert = 'success';
                    $mensaje[] = 'Asignacion Completa.';
                } else {
                    $alert = 'danger';
                    $mensaje[] = 'Debe seleccionar una Empresa';
                }
            } else {
                $alert = 'danger';
                $mensaje[] = 'Debe seleccionar una Oferta';
            }
        }

        return new ViewModel(
            array(
                'beneficios' => 'active',
                'oferta' => 'active',
                'oablock' => 'active',
                'form' => $form,
                'alert' => $alert,
                'msg' => $mensaje,
                'empresas' => $empresas,
                'ofertas' => $ofertas,
            )
        );
    }
}

