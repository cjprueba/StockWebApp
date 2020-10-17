<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel ;
use App\Exports\VentaMensualExport;
use App\Exports\VentaMensualMarcaCategoriaExport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use DateTime;


class VentaMensual extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reporte_mes:VentaMensual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia un reporte mensual de ventas';

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
        //
          //
                $dia2 = date("Y-m-d");
                $dia1 = new DateTime();
                $dia1->modify('first day of this month');
                $dia1= $dia1->format('Y-m-d');
        //

     
    Excel::store(new VentaMensualExport(), 'Venta_'.(string)$dia1.' al '.(string)$dia2.'.xlsx', 'local'); 
    Excel::store(new VentaMensualMarcaCategoriaExport(), 'Venta_Marca_Categoria_'.(string)$dia1.' al '.(string)$dia2.'.xlsx', 'local'); 
      $data = array(
        'name' => "Curso Laravel",
    );
            Mail::send('emails.VentasMensuales', $data, function ($message) use($dia1,$dia2){

        $message->from('tokutokuyapy@gmail.com', 'REPORTE MENSUAL DE VENTAS DEL DIA '.(string)$dia1.' al '.(string)$dia2);

        $message->to('jbonitakim@gmail.com')->subject('tokutokuya');
        //$message->attachFromStorage('/app/2020-06-09.xlsx');
        $message->attach('./storage/app/Venta_'.(string)$dia1.' al '.(string)$dia2.'.xlsx');
         $message->attach('./storage/app/Venta_Marca_Categoria_'.(string)$dia1.' al '.(string)$dia2.'.xlsx');
    });
    }
}
