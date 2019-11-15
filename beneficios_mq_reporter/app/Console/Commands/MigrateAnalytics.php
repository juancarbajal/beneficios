<?php

namespace App\Console\Commands;

use App\Library\Analytics;
use App\Library\ClienteMongo;
use App\Model\DmMetCliente;
use Illuminate\Console\Command;

class MigrateAnalytics extends Command
{
    protected $analytics;
    protected $mongo;
    const MAX_RESULT = 10000;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar datos de Analytics hacia MongoDB';

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
        /*$analytics_mongo = $this->mongo->getAnalytics();
        $fecha_ejecucion = date('Y-m-d H:00:00');
        echo "**************************************************\n";
        echo "Inicio de ejecución del script:". $fecha_ejecucion;
        echo "\n";

        $fechaInicio = strtotime("2016-01-12");
        $fechaFin = strtotime("2016-01-22");
        $bool = false;

        for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
            if ($bool) {
                echo date("Y-m-d", $i);
                echo "\n";
                $i-=86400;
                echo date("Y-m-d", $i);
                echo "\n";
                $bool = false;
            }

            try {
                $session_cat = $this->analytics->getResults(
                    date("Y-m-d", $i),
                    date("Y-m-d", $i),
                    array(
                        'dimensions' => 'ga:deviceCategory,ga:operatingSystem,ga:browser,ga:eventCategory,ga:eventAction,ga:eventLabel',
                        'filters' => 'ga:eventAction==login;ga:eventCategory!=ad;ga:eventCategory!=login;ga:eventCategory!=city;ga:eventLabel!=0;ga:eventLabel!=null;ga:eventCategory!=response.write(9765511*9024497)',
                        'max-results' => $this::MAX_RESULT,
                        'samplingLevel' => 'HIGHER_PRECISION'
                    )
                );

                $count = 0;
                foreach ($session_cat as $data) {
                    $data[] = date("Y-m-d", $i) . ' 00:00:00';
                    $data[4] = $data[5];
                    $data[5] = '';
                    $analytics_mongo->save($this->mongo->setAnalytics($data));
                    echo "Dispositivo: " . $data[0]. ", ";
                    echo "SO: " . $data[1]. ", ";
                    echo "Navegador: " . $data[2]. ", ";
                    echo "Id_Empresa: " . $data[3]. ", ";
                    echo "N° DOC: " . $data[4]. ", ";
                    echo "Slug: " . $data[5]. ", ";
                    echo "Evento: " . $data[6]. ", ";
                    echo "Evento Unico: " . $data[7]. ", ";
                    echo "Fecha: " . date("Y-m-d", $i);
                    echo "\n";
                    $count++;
                }
                echo $count;
                echo "\n";
            } catch (\Exception $e) {
                $bool = true;
            }
        }

        for($i=$fechaInicio; $i<=$fechaFin; $i+=86400){
            if ($bool) {
                echo date("Y-m-d", $i);
                echo "\n";
                $i-=86400;
                echo date("Y-m-d", $i);
                echo "\n";
                $bool = false;
            }

            try {
                $session_cat = $this->analytics->getResults(
                    date("Y-m-d", $i),
                    date("Y-m-d", $i),
                    array(
                        'dimensions' => 'ga:deviceCategory,ga:operatingSystem,ga:browser,ga:eventCategory,ga:eventAction,ga:eventLabel',
                        'filters' => 'ga:eventAction==categoria;ga:eventCategory!=ad;ga:eventCategory!=login;ga:eventCategory!=city;ga:eventLabel!=0;ga:eventLabel!=null;ga:eventCategory!=response.write(9765511*9024497)',
                        'max-results' => $this::MAX_RESULT,
                        'samplingLevel' => 'HIGHER_PRECISION'
                    )
                );

                $count = 0;
                foreach ($session_cat as $data) {
                    $data[] = date("Y-m-d", $i) . ' 00:00:00';
                    $data[4] = '';
                    $analytics_mongo->save($this->mongo->setAnalytics($data));
                    echo "Dispositivo: " . $data[0]. ", ";
                    echo "SO: " . $data[1]. ", ";
                    echo "Navegador: " . $data[2]. ", ";
                    echo "Id_Empresa: " . $data[3]. ", ";
                    echo "N° DOC: " . $data[4]. ", ";
                    echo "Slug: " . $data[5]. ", ";
                    echo "Evento: " . $data[6]. ", ";
                    echo "Evento Unico: " . $data[7]. ", ";
                    echo "Fecha: " . date("Y-m-d", $i);
                    echo "\n";
                    $count++;
                }
                echo $count;
                echo "\n";
            } catch (\Exception $e) {
                $bool = true;
            }
        }

        $fechaInicio = strtotime("2016-01-23");
        $fechaFin = strtotime(date("Y-m-d"));
        $bool = false;
        for($i=$fechaInicio; $i< $fechaFin; $i+=86400){
            if ($bool) {
                echo date("Y-m-d", $i);
                echo "\n";
                $i-=86400;
                echo date("Y-m-d", $i);
                echo "\n";
                $bool = false;
            }

            try {
                $session_cat = $this->analytics->getResults(
                    date("Y-m-d", $i),
                    date("Y-m-d", $i),
                    array(
                        'dimensions' => 'ga:deviceCategory,ga:operatingSystem,ga:browser,ga:eventCategory,ga:eventAction,ga:eventLabel',
                        'filters' => 'ga:eventCategory!=ad;ga:eventCategory!=login;ga:eventCategory!=city;ga:eventLabel!=0;ga:eventLabel!=null;ga:eventCategory!=response.write(9765511*9024497)',
                        'max-results' => $this::MAX_RESULT,
                        'samplingLevel' => 'HIGHER_PRECISION'
                    )
                );

                $count = 0;
                foreach ($session_cat as $data) {
                    $data[] = date("Y-m-d", $i) . ' 00:00:00';
                    $analytics_mongo->save($this->mongo->setAnalytics($data));
                    echo "Dispositivo: " . $data[0]. ", ";
                    echo "SO: " . $data[1]. ", ";
                    echo "Navegador: " . $data[2]. ", ";
                    echo "Id_Empresa: " . $data[3]. ", ";
                    echo "N° DOC: " . $data[4]. ", ";
                    echo "Slug: " . $data[5]. ", ";
                    echo "Evento: " . $data[6]. ", ";
                    echo "Evento Unico: " . $data[7]. ", ";
                    echo "Fecha: " . date("Y-m-d", $i);
                    echo "\n";
                    $count++;
                }
                echo $count;
                echo "\n";
            } catch (\Exception $e) {
                $bool = true;
            }

            if ($bool) {
                echo date("Y-m-d", $i);
                echo "\n";
            }
        }*/
        
    }
}
