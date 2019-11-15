<?php

namespace App\Console\Commands;

use App\Library\Analytics;
use App\Library\ClienteMongo;
use App\Model\DmMetCliente;
use Illuminate\Console\Command;

class UpdateMongo extends Command
{
    protected $analytics;
    protected $mongo;
    const MAX_RESULT = 10000;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mongo:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza datos de MongoDB segun data de Analytics';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->analytics = new Analytics();
        $this->mongo = new ClienteMongo();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fecha_ejecucion = date('Y-m-d H:00:00');
        echo "**************************************************\n";
        echo "Inicio de ejecución del script:" . $fecha_ejecucion;
        echo "\n";

        $fechaInicio = strtotime("2016-05-17");
        $fechaFin = strtotime("2016-05-17");
        $bool = false;
        $datos = array();
        $fecha_modificar = '2016-05-12'; //feca de donde se va a extraer la data a modificar

        for ($i = $fechaInicio; $i <= $fechaFin; $i += 86400) {
            if ($bool) {
                echo date("Y-m-d", $i);
                echo "\n";
                $i -= 86400;
                echo date("Y-m-d", $i);
                echo "\n";
                $bool = false;
            }

            try {
                $session_cat = $this->analytics->getResults_2(
                    date("Y-m-d", $i),
                    date("Y-m-d", $i),
                    array(
                        'dimensions' => 'ga:hostname',
                        'max-results' => $this::MAX_RESULT,
                        'samplingLevel' => 'HIGHER_PRECISION'
                    )
                );
                foreach ($session_cat as $data) {
                    if (strpos($data[0], 'beneficios')) {
                        $hostname = str_replace("www.", "", $data[0]);
                        $hostname = ($hostname != 'beneficios.pe') ? str_replace(".beneficios.pe", "", $hostname) : "";
                        $datos[] = array(
                            'hostname' => $hostname,
                            'visitas' => $data[1],
                            'visitasUnicas' => $data[2],
                            'fecha' => date("Y-m-d", $i)
                        );
                    }
                }
            } catch (\Exception $e) {
                $bool = true;
            }
        }
        $count = 0;
        foreach ($datos as $data) {
            $array = $this->mongo->get_collections($fecha_modificar, $data['hostname'], $data['visitas']);
            echo "HostName: " . $data['hostname'] . ", Visitas: " . $data['visitas'] . ", Fecha: " . $data['fecha'];
            echo "\n";
            echo "Registros Consultados: " . $array->count(true);
            echo "\n";
            foreach ($array as $dat) {
                $this->mongo->set_fechaRegistro($dat['_id'], $data['fecha']);
                //echo "Modificado: " . $dat['_id'] . ", Fecha: " . $data['fecha'];
                //echo "\n";
                $count++;
            }
            echo "Registros Modificados: " . $count;
            echo "\n";
            $count = 0;
        }
    }
}
