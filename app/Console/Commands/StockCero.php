<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel ;
use App\Exports\StockCeroDiarioExport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class StockCero extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reporte_dia:StockCero';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'se enviara un reporte diario de productos que se ceraron stock en el día';

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
                $dia = date("Y-m-d");
        //
     
    Excel::store(new StockCeroDiarioExport(), 'Stock_Cero_'.$dia.'.xlsx', 'local'); 
      $data = array(
        'name' => "Curso Laravel",
    );
         Mail::send('emails.stockcero', $data, function ($message) use($dia){

        $message->from('tokutokuyapy@gmail.com', 'Stock cerado del día: '.$dia.'');

        $message->to('aledanielxiaomi@gmail.com')->subject('tokutokuya');
        //$message->attachFromStorage('/app/2020-06-09.xlsx');
        $message->attach('./storage/app/Stock_Cero_'.$dia.'.xlsx');
    });

              // $message->attach('./storage/app/general.xlsx');
    }
}
