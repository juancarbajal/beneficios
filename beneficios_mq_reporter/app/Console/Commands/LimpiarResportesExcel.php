<?php

namespace App\Console\Commands;

use App\Model\ReporteExcel;
use Illuminate\Console\Command;

class LimpiarResportesExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean:limpiar-registros-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar registros excel en un periodo dado';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
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
        echo "Inicio de ejecución del script:". $fecha_ejecucion;
        echo "\n";

        $nro_dias = env('DAY') ? env('DAY') : 15 ;
        echo "Periodo de tiempo:".$nro_dias;
        echo "\n";

        $fecha_filtro = strtotime ( '-'.$nro_dias.' day' , strtotime ( $fecha_ejecucion ) ) ;
        $fecha_filtro = date ( 'Y-m-d H:00:00' , $fecha_filtro );
        echo "Fecha de Filtro : ".$fecha_filtro;
        echo "\n";

        $lista = ReporteExcel::where('created_at','<=',$fecha_filtro)->get();

        $lista_ids =  array();

        foreach ($lista as $item){
            array_push($lista_ids, (int) $item->id);
        }

        $nro_ids = count($lista);

        echo "Nro de Archivos de Excel a eliminar: ".$nro_ids;
        echo "\n";


        echo "Lista de Ids de Reporte de Excel : [".implode(",",$lista_ids)."]   ";
        echo "\n";

        echo "Inicio de Proceso de Eliminación";

        if($nro_ids){
            echo "\n\n";

            foreach($lista as $l){

                echo "Archivo id:".$l->id;
                echo "\n";
                echo "Archivo nombre: '".$l->name."'";
                echo "\n";

                $path = public_path(). '/descargas/' . $l->name;
                if (file_exists($path)) {
                    unlink($path);
                }
                $l->delete();
            }

            echo "\n";
        }else{
            echo "\n---- No existe Archivos ----\n";
        }

        echo "Fin de Proceso de Eliminación";
        echo "\n";

        echo "Fin de ejecución del script   :". $fecha_ejecucion;
        echo "\n**************************************************\n";
    }
}
