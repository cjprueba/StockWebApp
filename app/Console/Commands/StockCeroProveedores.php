<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel ;
use App\Exports\StockCeroProveeExport;
use App\Exports\StockCeroProvee;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailable;
use DateTime;

class StockCeroProveedores extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reporte_quincena:StockCeroProvee';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'enviara un reporte cada 2 semanas de stock cero de otros proveedores que no sean de tokutokuya';

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
        $dia2 = date("Y-m-d");
                $dia1 = new DateTime();
                $dia1->modify('first day of this month');
                $dia1= $dia1->format('Y-m-d');
        //
            Excel::store(new StockCeroProveeExport(), 'Stock_Cero_'.(string)$dia1.' al '.(string)$dia2.'.xlsx', 'local'); 
    }
}
