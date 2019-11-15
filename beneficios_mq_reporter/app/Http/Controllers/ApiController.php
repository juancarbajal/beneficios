<?php

namespace App\Http\Controllers;


use App\Jobs\ReportCRM;
use App\Jobs\ReporteCRM2;
use App\Jobs\ReportDescargas;
use App\Library\ClienteMongo;
use App\Model\EmpresaClienteCliente;
use Illuminate\Support\Facades\Request;

class ApiController extends Controller
{
    private $mongo;

    public function __construct()
    {
        $this->mongo = new ClienteMongo();
    }

    public function reporte_crm()
    {
        $data = Request::all();

        $rules = array(
            'fecha_inicio' => 'required|date_format:Y-m-d',
            'id_empresa' => 'integer|exists:BNF_Empresa,id',
            'emails' => 'required',
            'fecha_fin' => 'required|date_format:Y-m-d'
        );

        $v = \Validator::make($data, $rules);
        if ($v->fails()) {
            return response()->json(['error' => 1]);
        }
        $emails = explode(',', $data['emails']);
        $numero = count($emails);
        $num = 0;
        $rules_2 = array(
            'email' => 'required|email',
        );
        foreach ($emails as $e) {
            $v = \Validator::make(['email' => $e], $rules_2);
            if (!$v->fails()) {
                $num++;
            }
        }

        if ($numero != $num) {
            return response()->json(['error' => 1]);
        }
        $date_register = date("Y-m-d H:i:s");
        $nombre_excel = $date_register . '-reporte_3.xlsx';

        $this->dispatch(
            new ReportCRM(
                $data['fecha_inicio'],
                $data['fecha_fin'],
                $data['id_empresa'],
                $date_register,
                $nombre_excel,
                $emails
            )
        );

        // $a = new Funciones();
        //$a->exportacion($data['fecha_inicio'], $data['fecha_fin'], $data['id_empresa'], $data['id_usuario'], $date_register);

        return response()->json(array('error' => 0));
    }

    public function reporte_crm_2()
    {
        $data = Request::all();

        $rules = array(
            'fecha_inicio' => 'required|date_format:Y-m-d',
            'id_empresa' => 'integer|exists:BNF_Empresa,id',
            'emails' => 'required',
            'fecha_fin' => 'required|date_format:Y-m-d',
            'costo' => '',
            'meta' => ''
        );

        $v = \Validator::make($data, $rules);

        if ($v->fails()) {
            return response()->json(['error' => 1]);
        }

        $emails = explode(',', $data['emails']);
        $numero = count($emails);
        $num = 0;
        $rules_2 = array(
            'email' => 'required|email',
        );

        foreach ($emails as $e) {
            $v = \Validator::make(['email' => $e], $rules_2);
            if (!$v->fails()) {
                $num++;
            }
        }

        if ($numero != $num) {
            return response()->json(['error' => 1]);
        }

        $nombre_empresa = "";
        if (!empty($data['id_empresa'])) {
            $getEmpresaCCTable = new EmpresaClienteCliente();
            $data_empresa = $getEmpresaCCTable->getEmpresaName($data['id_empresa'])[0];
            $nombre_empresa = $data_empresa->NombreComercial . "-";
        }

        $date_register = date("Y-m-d H:i:s");
        $nombre_excel = $nombre_empresa . $date_register . '.xlsx';

        $this->dispatch(
            new ReporteCRM2(
                $data['fecha_inicio'],
                $data['fecha_fin'],
                $data['id_empresa'],
                $data['costo'],
                $data['meta'],
                $date_register,
                $nombre_excel,
                $emails
            )
        );

        return response()->json(array('error' => 0));
    }

    public function guardar_crm()
    {
        $data = Request::all();

        $rules = array(
            'dni' => 'required|exists:BNF_Cliente,NumeroDocumento',
            'id_empresa' => 'required|exists:BNF_Empresa,id',
            'categoria' => 'required|exists:BNF_Categoria,Slug'
        );

        $v = \Validator::make($data, $rules);
        if ($v->fails()) {
            dd($v->errors());
            return response()->json(['error' => 1]);
        }

        $data['ip'] = request()->server()['REMOTE_ADDR'];

        $this->mongo->actualizar_analytics($data);

        return response()->json(array('error' => 0));
    }

    public function reporte_descargas()
    {
        $data = Request::all();

        $rules = array(
            'fecha_inicio' => 'required|date_format:Y-m-d',
            'id_empresa' => 'integer|exists:BNF_Empresa,id',
            'emails' => 'required',
            'fecha_fin' => 'required|date_format:Y-m-d'
        );

        $v = \Validator::make($data, $rules);
        if ($v->fails()) {
            return response()->json(['error' => 1]);
        }

        $emails = explode(',', $data['emails']);
        $numero = count($emails);
        $num = 0;
        $rules_2 = array(
            'email' => 'required|email',
        );

        foreach ($emails as $e) {
            $v = \Validator::make(['email' => $e], $rules_2);
            if (!$v->fails()) {
                $num++;
            }
        }

        if ($numero != $num) {
            return response()->json(['error' => 1]);
        }

        $nombre_empresa = "";
        if (!empty($data['id_empresa'])) {
            $getEmpresaCCTable = new EmpresaClienteCliente();
            $data_empresa = $getEmpresaCCTable->getEmpresaName($data['id_empresa'])[0];
            $nombre_empresa = $data_empresa->NombreComercial . "-";
        }

        $date_register = date("Y-m-d H:i:s");
        $nombre_excel = $nombre_empresa . $date_register . '.xlsx';

        $this->dispatch(
            new ReportDescargas(
                $data['fecha_inicio'],
                $data['fecha_fin'],
                $data['id_empresa'],
                $date_register,
                $nombre_excel,
                $emails
            )
        );
        
        /*$a = new Funciones();
        $a->exportacion_3($data['fecha_inicio'], $data['fecha_fin'], $data['id_empresa'], $nombre_excel);*/

        return response()->json(array('error' => 0));
    }
}
