<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel ;
use App\Exports\VentasDiariasExport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
class CadaDia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reporte_dia:enviar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se realizara un reporte de venta diario para enviar por correo electronico';

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
         $dia = '2020-06-13';
        //
     
        Excel::store(new VentasDiariasExport(), 'general.xlsx', 'local'); 
      $data = array(
        'name' => "Curso Laravel",
    );
            Mail::send('emails.welcome', $data, function ($message) use($dia){

        $message->from('tokutokuyapy@gmail.com', 'REPORTE GENERAL');

        $message->to('aledanielxiaomi@gmail.com')->subject('tokuokuya');
        //$message->attachFromStorage('/app/2020-06-09.xlsx');
        $message->attach('./storage/app/general.xlsx');
    });


    }
}
