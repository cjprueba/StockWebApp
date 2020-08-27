<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel ;
use App\Exports\StockMinimoExport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;

class StockMinimo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reporte_dia:StockMinimo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'enviara un reporte informativo sobre los productos con stock Minimo en la tienda';

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
                $dia = date("Y-m-d");
        //
     
    Excel::store(new StockMinimoExport(), 'Stock_minimo_'.$dia.'.xlsx', 'local'); 
      $data = array(
        'name' => "Curso Laravel",
    );
         Mail::send('emails.stockminimo', $data, function ($message) use($dia){

        $message->from('tokutokuyapy@gmail.com', 'Stock minimo del día: '.$dia.'');

        $message->to('aledanielxiaomi@gmail.com')->subject('tokutokuya');
        //$message->attachFromStorage('/app/2020-06-09.xlsx');
        $message->attach('./storage/app/Stock_minimo_'.$dia.'.xlsx');
    });
    }
}
