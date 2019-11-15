<?php

namespace Application\Controller;

use Application\Cache\CacheManager;
use Application\Model\DetalleOfertaFormulario;
use Application\Model\OfertaFormCliente;
use Application\Model\OfertaFormClienteLead;
use Application\Service\MobileDetect;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as SessionContainer;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Debug\Debug;

class LeadController extends AbstractActionController
{
    protected $empresatable;
    protected $ubigeotable;
    protected $categoriatable;
    protected $ofertatable;
    protected $ofertaformulariotable;
    protected $detalleOfertaFormulariotable;
    protected $ofertaformcliente;
    protected $clientetable;
    protected $configuraciones;
    protected $categoriaTable;
    protected $ofertaformLeadtable;
    protected $formularioLeadTable;
    private $renderer;
    const URL_RESULTADO = 'resultado/home';
    const TIPO_BUSQUEDA = 1;
    const TIPO_MENSAJE_ERROR = 'danger';
    const TIPO_MENSAJE_VALIDO = 'success';
    const OFERTA_TIPO_SPLIT = "Split";

    public function getUbigeo()
    {
        return $this->identity()['ubigeo'];
    }

    public function setUbigeo($ubigeo)
    {
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');
        $data_user['ubigeo'] = $ubigeo;
        $session->offsetSet('storage', $data_user);
    }

    public function getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo)
    {
        $this->ofertatable = $this->serviceLocator->get('Application\Model\Table\OfertaTable');
        $data = array();
        $config = $this->getServiceLocator()->get('Config');
        $ttl = $config['items']['Empresas']['getAfiliados']['ttl'];
        $cacheM = new CacheManager($config['connection_cache']);
        $cache = $cacheM->getCache(__CLASS__, __FUNCTION__, $ttl);
        $cacheStatus = $config['cache_status'];
        $keyAFIL = __CLASS__ . __FUNCTION__ . $segmento . $ubigeo_id . $empresa . $subgrupo;
        if ($cache->hasItem($keyAFIL) and $cacheStatus == true) {
            $logos = $cache->getItem($keyAFIL);
        } else {
            $logos = $this->ofertatable
                ->getLogoEmpresaXOferta($segmento, $ubigeo_id, $empresa, $subgrupo)
                ->toArray();
            $cache->setItem($keyAFIL, $logos);
        }

        $empresas_afiliadas = array();
        foreach ($logos as $dato) {
            $logo['logo'] = $dato['Nombre'];
            $logo['slug'] = $dato['Slug'];
            array_push($empresas_afiliadas, $logo);
        }

        $count = count($empresas_afiliadas);
        if ($count > 25) {
            $rand = rand(1, $count - 1);
            for ($i = 0; $i < 25; $i++) {
                $data[] = $empresas_afiliadas[$rand + $i];
                if ($rand + $i == $count - 1) {
                    $rand = -($i + 1);
                }
            }
        } else {
            $data = $empresas_afiliadas;
        }

        return $data;
    }

    public function __construct()
    {
        $session = new SessionContainer('auth');
        $data_user = $session->offsetGet('storage');

        if (!isset($data_user['NumeroDocumento'])) {
            header('Location: /');
        }
    }


    public function getOfertaEmpresaClienteTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaEmpresaClienteTable');
    }
    public function getOfertaAtributosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaAtributosTable');
    }
    public function OfertaAtributosTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\OfertaAtributosTable');

    }
    public function getCuponTable()
    {
        return $this->serviceLocator->get('Application\Model\Table\CuponTable');
    }
    public function indexAction()
    {
            $meses = array(
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            );
            $this->empresatable = $this->serviceLocator->get('Auth\Model\Table\EmpresaTable');
            $this->ubigeotable = $this->serviceLocator->get('Application\Model\UbigeoTable');
            $this->categoriatable = $this->serviceLocator->get('Application\Model\Table\CategoriaTable');
            $this->ofertatable = $this->serviceLocator->get('Application\Model\Table\OfertaTable');
            $this->ofertaformulariotable = $this->serviceLocator->get('Application\Model\Table\OfertaFormularioTable');
            $this->detalleOfertaFormulariotable = $this->serviceLocator
                ->get('Application\Model\Table\DetalleOfertaFormularioTable');
            $this->ofertaformcliente = $this->serviceLocator->get('Application\Model\Table\OfertaFormClienteTable');
            $this->clientetable = $this->serviceLocator->get('Auth\Model\Table\ClienteTable');
            $this->configuraciones = $this->serviceLocator->get('Application\Model\Table\ConfiguracionesTable');
            $this->categoriaTable = $this->serviceLocator->get('Application\Model\Table\CategoriaTable');
            $this->formularioLeadTable = $this->serviceLocator->get('Application\Model\Table\FormularioLeadTable');
            $this->ofertaformLeadtable = $this->serviceLocator->get('Application\Model\Table\OfertaFormClienteLeadTable');

            $dataoferta=array();
            $nombre = null;
            $ofertas = null;
            $estado = null;
            $condicionesTexto = null;
            $condicionesEstado = null;
            $nombreempresa = null;
            $id = 0;
            $select = -1;
            $pais = 1;
            $envio = null;
            $message = array();
            $datos = array();
            $atributo=null;
            $title=null;
            $otros = array();
            $type = $this::TIPO_MENSAJE_ERROR;
            $active = true;
            $data_recovered = array();
            $array = array('bus', 'com', 'cam', 'tie');

            $slug = $this->params()->fromRoute('opt', 0);
            $category = $this->params()->fromRoute('val', null);
            $slug_cat = $this->getRequest()->getPost('slugcat');
            $cat = $this->categoriaTable->getBuscarCategoria($slug_cat);

            $id_Categoria = (!$cat) ? substr($slug_cat, 0, 3) : $cat->id;
            $ubigeo_id = $this->getUbigeo();
            $catotros = $this->categoriatable->getBuscarCatOtros($pais);


            $dni = $this->identity()['NumeroDocumento'];
            $empresa = $this->identity()['Empresa'];
            $img = $this->identity()['logo'];
            $subgrupo = $this->identity()['subgrupo'];
            $segmento = $this->identity()['segmento'];

            //Configuraciones
            $terminoscondiciones = $this->configuraciones->getConfig('terminoscondicioneslead');
            $mensaje_confirmacion = $this->configuraciones->getConfig('mensaje_confirmacion_lead');
            $textobanner = $this->configuraciones->getConfig('textobannerlead');

            //ubicacion
            $ubigeos = $this->ubigeotable->getUbigeo($ubigeo_id);
            $ubigeo = $ubigeos->Nombre;

            //empresas afiliadas
            $data = $this->getAfiliados($segmento, $ubigeo_id, $empresa, $subgrupo);

            //categorias
            $categorias = $this->categoriatable->getBuscarCategoriaXPais($pais);
            $categoriasfooter = $this->categoriatable->getBuscarCategoriaXPais($pais);

            //varibles globales
            $config = $this->getServiceLocator()->get('Config');
            $oferta_lead = $this->ofertaformulariotable->getFormularios($slug);

            foreach ($oferta_lead as $dato) {
                $nombreempresa = $dato->NombreComercial;
                $id = $dato->oferta_id;
                $estado = $dato->Estado;
            }

            if ($id == 0) {
                return $this->redirect()->toRoute('404', array('opt' => $slug));
            }

            //Datos Condiciones Delivery
            $oferta = $this->ofertatable->getOferta($id);


            $atributosData = null;

            $cupon = $this->getOfertaEmpresaClienteTable()->getOfertaDetalle($id);

            if (!is_null($oferta->TipoAtributo)) {
                $atributosData = $this->OfertaAtributosTable()->getAllOfertaAtributos($id);

            }


            $condicionesTexto = $oferta->CondicionesDeliveryTexto;
            $condicionesEstado = $oferta->CondicionesDeliveryEstado;

            //Datos del Formulario Lead
            $form_lead = $this->formularioLeadTable->getFormulario($id);
            $form_config = $this->ofertaformulariotable->getFormularios($slug);
            $mensaje_correo = "";
            foreach ($form_config as $item) {
                if ($item->Descripcion == "textobanner") {
                    $mensaje_correo = $item->valor;
                }
            }

            if (isset($_SERVER['HTTP_REFERER'])) {
                $url = $_SERVER['HTTP_REFERER'];
            } else {
                $url = "http://" . $_SERVER['HTTP_HOST'];
            }

            $parsedUrl = parse_url($url);
            $host = explode('.', $parsedUrl['host']);
            $subdomain = $host[0];

            $request = $this->getRequest();
            if ($request->isPost()) {

                $atributo = (int)$request->getPost('atributo');
                $title=   ($oferta->TipoAtributo == $this::OFERTA_TIPO_SPLIT)?
                    $request->getPost('title'):
                    null;

                $idEmpresa = $request->getPost('idEmpresa');


                if (!$cat && !in_array($id_Categoria, $array)) {
                    return $this->redirect()->toRoute('404');
                }

                $category = $this->getRequest()->getPost('slugcat');

                $id = $request->getPost()->id;
                $estaticos = array();
                $claveslead = array();

                //Recuperamos los campos requeridos
                $requerido = array();
                $form_config_data = $this->ofertaformulariotable->getFormularios($slug);
                foreach ($form_config_data as $datoR) {
                    if ($datoR->Activo == 1) {
                        $requerido[$datoR->id] = $datoR->Requerido;
                        $estaticos = $this->createStatics($datoR->Descripcion, $datoR->id, $estaticos);
                    }
                }

                //Recuperamos los campos dinamicos requeridos
                $requeridoDynamic = array();
                $form_lead_data = $this->formularioLeadTable->getFormulario($id);

                foreach ($form_lead_data as $datoR) {

                    if ($datoR->Activo == 1) {
                        $nombre_camp = trim($datoR->Nombre_Campo);
                        $nombre_camp = str_replace(" ", "_", $nombre_camp);
                        $requeridoDynamic[$nombre_camp] = $datoR->Requerido;
                        $claveslead[$nombre_camp] = array(
                            "id" => $datoR->id,
                            "nombre" => $datoR->Nombre_Campo
                        );
                    }
                }


                //Validando Datos
                foreach ($request->getPost() as $key => $dato) {

                    $fragmentos = explode("_", $key);
                    $camp = reset($fragmentos);
                    $post = end($fragmentos);

                    $data_recovered[$key] = $dato;
                    if (array_key_exists($post, $requerido)) {
                        if ($requerido[$post] == "1" && $dato == null) {
                            switch ($camp) {
                                case "name":
                                    $message['msg_' . $camp] = 'Ingrese su nombres y apellidos';
                                    $message['msg_e' . $camp] = 'error';
                                    break;
                                case "dir":
                                    $message['msg_' . $camp] = 'Ingrese su dirección';
                                    $message['msg_e' . $camp] = 'error';
                                    break;
                                case "tel":
                                    $message['msg_' . $camp] = 'Ingrese su número de telefono';
                                    $message['msg_e' . $camp] = 'error';
                                    break;
                                case "email":
                                    $message['msg_' . $camp] = 'Ingrese su email';
                                    $message['msg_e' . $camp] = 'error';
                                    break;
                                case "gen":
                                    $message['msg_' . $camp] = 'Ingrese su genero';
                                    $message['msg_e' . $camp] = 'error';
                                    break;
                                case "dep":
                                    $message['msg_' . $camp] = 'Seleccione un departamento';
                                    $message['msg_e' . $camp] = 'error';
                                    break;
                                case "prov":
                                    $message['msg_' . $camp] = 'Seleccione una provincia';
                                    $message['msg_e' . $camp] = 'error';
                                    break;
                                case "ciu":
                                    $message['msg_' . $camp] = 'Ingrese su Ciudad actual';
                                    $message['msg_e' . $camp] = 'error';
                                    break;
                                case "hor":
                                    $message['msg_' . $camp] = 'Ingrese su horario de Atención';
                                    $message['msg_e' . $camp] = 'error';
                                    break;
                                case "tipo":
                                    $message['msg_' . $camp] = 'Ingrese su tipo de Contacto';
                                    $message['msg_e' . $camp] = 'error';
                                    break;
                            }
                        }
                    }

                    if (array_key_exists($key, $requeridoDynamic)) {
                        if ($requeridoDynamic[$key] == "1" && $dato == null) {
                            $message['msg_' . $key] = 'El campo no puede quedar vacío';
                            $message['msg_e' . $key] = 'error';
                        }
                    }
                }

                if ($message == array()) {
                    $oferta = $this->ofertatable->getOferta($id);

                    $emailempresa = null;
                    foreach ($this->ofertaformulariotable->getFormularios($slug) as $dato) {
                        if ($dato->Descripcion == 'CorreoContacto') {
                            $emailempresa = $dato->valor;
                        }
                    }

                    $idCliente = $this->identity()['id'];
                    $id_Empresa = $this->identity()['Empresa'];
                    $maxDescargas = $oferta->DescargaMaximaDia;

                    $idOEC = $this->getOfertaEmpresaClienteTable()->getOfertaEmpresaCliente($id, $idEmpresa);



                    $resultado = $this->ofertaformcliente->verifyLimit($id, $idOEC->id, $idCliente, $atributo);

                    if ($resultado < $maxDescargas) {

                        $stock=($oferta->TipoAtributo == $this::OFERTA_TIPO_SPLIT)?
                                $this->getOfertaAtributosTable()->getOfertaAtributos($atributo)->Stock:
                                $oferta->Stock;

                        if ($stock > 0 && $emailempresa != null) {
                            $datos['Oferta'] = $oferta->TituloCorto;

                            $dataPost=(array)$request->getPost();
                            unset($dataPost['title']); unset($dataPost['idEmpresa']);unset($dataPost['atributo']);

                            foreach ($dataPost as $key => $dato) {

                                $fragmentos = explode("_", $key);

                                $post = end($fragmentos);

                                if ($key == 'dep_' . $post && $dato != null) {
                                    $dato = $this->ubigeotable->getUbigeo($dato)->Nombre;
                                }
                                if ($key == 'prov_' . $post && $dato != null) {
                                    $dato = $this->ubigeotable->getUbigeo($dato)->Nombre;
                                }


                                $campoform = reset($fragmentos);
                                $idcampo = end($fragmentos);
                                $existe = in_array($key, $estaticos);

                                if ($campoform != 'id' && $campoform != 'slugcat' && $existe == true) {

                                    $flag = ($campoform == "email") ? false : true;
                                    $dato = $this->setSanitaze($dato, $flag);
                                    $datos[$this->detalleOfertaFormulariotable->getDescripcionFormulario($idcampo)] = $dato;
                                    $detalleOfertaFormulario = new DetalleOfertaFormulario();
                                    $detalleOfertaFormulario->BNF_OfertaFormulario_id = $idcampo;
                                    $detalleOfertaFormulario->Descripcion = $dato;

                                    $this->detalleOfertaFormulariotable
                                        ->saveDetalleOfertaFormulario($detalleOfertaFormulario);
                                } elseif ($campoform != 'id' && $campoform != 'slugcat' && $existe == false) {

                                    $dato = $this->setSanitaze($dato);
                                    $ofertaFormCliente = new OfertaFormClienteLead();
                                    $ofertaFormCliente->BNF_Oferta_id = $id;
                                    $ofertaFormCliente->BNF_Cliente_id = $idCliente;
                                    $ofertaFormCliente->BNF_Empresa_id = $id_Empresa;
                                    $ofertaFormCliente->BNF_Categoria_id = $id_Categoria;
                                    $ofertaFormCliente->BNF_Formulario_id = $claveslead[$key]["id"];
                                    $ofertaFormCliente->Descripcion = $dato;
                                    $this->ofertaformLeadtable->saveFormulario($ofertaFormCliente);
                                    $otros[$claveslead[$key]["nombre"]] = $dato;
                                }
                            }

                            $titleSplit=null;
                            if ($oferta->TipoAtributo == $this::OFERTA_TIPO_SPLIT) {
                                $dataAtributo = $this->getOfertaAtributosTable()->getOfertaAtributos($atributo);
                                $dataAttr['Stock'] = (int)$dataAtributo->Stock - 1;
                                $this->getOfertaAtributosTable()
                                    ->updateOfertaAtributos($dataAttr, $oferta->id, $atributo);

                                if ($dataAttr['Stock'] == 0) {
                                    $totalAtributos = $this->getOfertaAtributosTable()->getTotalHabilitados($oferta->id);
                                    if ($totalAtributos == 0) {
                                        $dataoferta['Estado'] = 'Caducado';
                                        $this->ofertatable->updateOferta($dataoferta, $oferta->id);
                                    }
                                }
                            } else {
                                $dataoferta['Stock'] = (int)$oferta->Stock - 1;
                                if ($dataoferta['Stock'] == 0) {
                                    $dataoferta['Estado'] = 'Caducado';
                                }
                                $this->ofertatable->updateOferta($dataoferta, $id);
                            }


                            $envio[] = '<h5>Gracias, ' . $mensaje_confirmacion->Atributo . '</h5>';
                            $type = $this::TIPO_MENSAJE_VALIDO;
                            $active = false;

                            $ofertaFormCliente = new OfertaFormCliente();
                            $ofertaFormCliente->BNF_Cliente_id = $idCliente;
                            $ofertaFormCliente->BNF_Oferta_id = $id;
                            $ofertaFormCliente->BNF_Empresa_id = $id_Empresa;
                            $ofertaFormCliente->BNF_Categoria_id = $id_Categoria;
                            $ofertaFormCliente->BNF_OfertaEmpresaCliente_id = $idOEC->id;
                            $ofertaFormCliente->BNF_Oferta_Atributo_id = ($oferta->TipoAtributo == $this::OFERTA_TIPO_SPLIT)?
                                                                            $atributo:null;
                            $this->ofertaformcliente->saveOfertaFormCliente($ofertaFormCliente);

                            $transport = $this->getServiceLocator()->get('mail.transport');

                            $empresa = $this->empresatable->getEmpresa($empresa);
                            if ($empresa->SubDominio != $subdomain) {
                                $subdomain = "beneficios.pe";
                            }

                            $this->renderer = $this->getServiceLocator()->get('ViewRenderer');
                            $content = $this->renderer->render(
                                'Application/mail/lead',
                                array(
                                    'datos' => $datos,
                                    'otros' => $otros,
                                    'titleSplit'=>$title,
                                    'mensaje' => $mensaje_correo,
                                    'subdominio' => $subdomain,
                                )
                            );

                            $messageemail = new Message();

                            $messageemail->addTo($emailempresa)
                                ->addFrom('weeareroialty@gmail.com', 'weeare')
                                ->setSubject('Registro de Cliente');

                            $htmlBody = new MimePart($content);
                            $htmlBody->type = "text/html";
                            $body = new MimeMessage();
                            $body->setParts(array($htmlBody));
                            $messageemail->setBody($body);
                            $transport->send($messageemail);
                        } else {
                            $envio[] = '<h5>Lo Sentimos, la Oferta se Agotó</h5>';
                            $active = false;
                        }
                    } else {
                        $envio[] = '<h5>Lo Sentimos, ha llego al máximo de envios permitidos en el día</h5>';
                        $active = false;
                    }
                }
            }

            $departamentos = $this->ubigeotable->fetchAllDepartamentXPais($pais);
            $provincias = $this->ubigeotable->fetchAllProvince();

            $view = new ViewModel();
            $view->setVariables(
                array(
                    'url' => $this::URL_RESULTADO,
                    'router' => 'lead',
                    'slug' => $slug,
                    'category' => $category,
                    'estado' => $estado,
                    'rlogos' => $config['images']["logos"],
                    'rofertas' => $config['images']["ofertas"],
                    'rgaleria' => $config['images']["galeria"],
                    'rbanners' => $config['images']["banners"],
                    'terminos' => $terminoscondiciones->Atributo,
                    'textobanner' => $textobanner->Atributo,
                    'nombre' => $nombre,
                    'atributo'=>$atributo,
                    'cliente' => $dni,
                    'imgemp' => $img,
                    'ubigeo' => $ubigeo,
                    'data' => $data,
                    'meses' => $meses,
                    'title'=>$title,
                    'categorias' => $categorias,
                    'categoriasfooter' => $categoriasfooter,
                    'typebusqueda' => $this::TIPO_BUSQUEDA,
                    'ofertas' => $ofertas,
                    'ubigeo_id' => $ubigeo_id,
                    'oferta' => $oferta,
                    'cupon' => $cupon,
                    'atributosData' => $atributosData,
                    'select' => $select,
                    'campania_id' => null,
                    'nombreempresa' => $nombreempresa,
                    'form_config' => $form_config,
                    'id' => $id,
                    'message' => $message,
                    'confir' => $envio,
                    'catotros' => $catotros,
                    'type' => $type,
                    'departamentos' => $departamentos,
                    'provincias' => $provincias,
                    'active' => $active,
                    'afiliadas' => true,
                    'form_lead' => $form_lead,
                    'data_recovered' => $data_recovered,
                    'condicionesTexto' => $condicionesTexto,
                    'condicionesEstado' => $condicionesEstado,
                )
            );

            $mobile = new MobileDetect();
            if ($mobile->isMobile() == 1) {
                $view->setTemplate('application/lead/index-mobile');
            }
            return $view;

    }

    public function setSanitaze($cadena, $flag = true)
    {
        $a = array(
            'S/.', '!', '¡', ' + ', '®', '#', ':', ',', '/', ';', '*', '\\', '$', '%', '', '©', '£', '¥',
            '|', '°', '¬', '"', '&', '(', ')', '?', '¿', "'", '{', '}', '^', '~', '`', '<', '>', '-.', '.-', '--',
            ' a ', ' e ', ' i ', ' o ', ' u ', 'á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú', 'ñ'
        );

        if ($flag == false) {
            array_merge($a, array('@', '.'));
        }
        return strtolower(str_ireplace($a, "", strtolower($cadena)));
    }

    public function createStatics($data, $id, $array)
    {
        switch ($data) {
            case "Nombres y Apellidos":
                $array[] = "name_" . $id;
                break;
            case "Dirección":
                $array[] = "dir_" . $id;
                break;
            case "Teléfono":
                $array[] = "tel_" . $id;
                break;
            case "Email":
                $array[] = "email_" . $id;
                break;
            case "Género":
                $array[] = "gen_" . $id;
                break;
            case "Departamento":
                $array[] = "dep_" . $id;
                break;
            case "Provincia":
                $array[] = "prov_" . $id;
                break;
            case "Ciudad":
                $array[] = "ciu_" . $id;
                break;
            case "Horario de Contacto":
                $array[] = "hor_" . $id;
                break;
            case "Tipo de Contacto":
                $array[] = "tipo_" . $id;
                break;
        }
        return $array;
    }
}
