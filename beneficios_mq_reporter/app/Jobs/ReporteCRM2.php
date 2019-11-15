<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Library\Funciones;
use App\Model\Usuario;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Support\Facades\Config;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ReporteCRM2 extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $id_empresa;
    protected $fecha_inicio;
    protected $costo;
    protected $meta;
    protected $fecha_fin;
    protected $nombre_excel;
    protected $fecha_registro;
    protected $emails;
    protected $funciones;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($fecha_inicio, $fecha_fin, $id_empresa,$costo,$meta, $fecha_registro, $nombre_excel,$emails)
    {
        $this->id_empresa = $id_empresa;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->costo = $costo;
        $this->meta = $meta;
        $this->nombre_excel = $nombre_excel;
        $this->fecha_registro = $fecha_registro;
        $this->emails = $emails;
        $this->funciones = new Funciones();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Mailer $mailer)
    {
        $attempts = $this->job ? $this->job->attempts() : 1;
        $emails = $this->emails;
        $route = env('URL_WEB');
        $title = "Reporte de Uso Anterior";

        $error_message = "" ;

        $data_send =  array(
            'date_send' => $this->fecha_registro,
            'name_excel' => $this->nombre_excel,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin
        );

        if ($attempts < 2){
            try{
                $this->funciones->exportacion_2(
                    $this->fecha_inicio,
                    $this->fecha_fin,
                    $this->id_empresa,
                    $this->costo,
                    $this->meta,
                    $this->nombre_excel
                );


                foreach ($emails as $e){
                    $mailer->send('emails.reporte', ['route' => $route, 'date_send' =>  $this->fecha_registro, 'name_excel' => $this->nombre_excel, 'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin],
                        function ($message) use ($e, $title) {
                            $message->to($e)->subject($title);
                        }
                    );
                }
                $this->job->delete();

            }catch (\Exception $e){
                $message = "Archivo:".$e->getFile()." - Linea:". $e->getLine()." - Mensaje:". $e->getMessage();
                $error_message = "[".date("Y-m-d H:i:s")."] ". $message."  DATA: ";
                $error_message .= "'fecha_registro': '". $this->fecha_registro."'";
                $error_message .= ", 'correos': '".implode("','",$emails)."'" ;
                $error_message .= ", 'nombre_archivo': '".$this->nombre_excel ."'" ;
                echo $error_message;
                echo "\n";
                $this->job->release();
            }
        } else {
            $message = "Error mas de una Iteracion";
            $error_message = "[".date("Y-m-d H:i:s")."] ". $message."  DATA: ";
            $error_message .= "'fecha_registro': '". $this->fecha_registro."'";
            $error_message .= ", 'correos': '".implode("','",$emails)."'" ;
            $error_message .= ", 'nombre_archivo': '".$this->nombre_excel ."'" ;
            echo $error_message;
            echo "\n";
        }

        if($error_message){
            $this->job->delete();

            try {
                $email = Config::get('mail.from.address_error');
                $data_send['error'] = $error_message;
                $data_send['type_report'] = "Reporte de Uso";
                $title = "Error en los reportes";
                $mailer->send('emails.reporte_error', $data_send,
                    function ($message) use ($email, $title) {
                        $message->to($email)->subject($title);
                    }
                );
            } catch(\Exception $e) {
                $message = "Archivo:".$e->getFile()." - Linea:". $e->getLine()." - Mensaje:". $e->getMessage();
                $error_message = "[".date("Y-m-d H:i:s")."] ". $message."  DATA: ";
                $error_message .= "'fecha_registro': '". $this->fecha_registro."'";
                $error_message .= ", 'correos': '".implode("','",$emails)."'" ;
                $error_message .= ", 'nombre_archivo': '".$this->nombre_excel ."'" ;
                echo $error_message;
                echo "\n";
            }
        }
    }
}
