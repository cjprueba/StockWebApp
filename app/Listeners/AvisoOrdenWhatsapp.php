<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Twilio\Rest\Client;

class AvisoOrdenWhatsapp
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        
        /*  --------------------------------------------------------------------------------- */

        $sid    = env("TWILIO_SID");
        $token  = env("TWILIO_TOKEN");
        $twilio = new Client($sid, $token);

        $data = "ID: *".$event->orden['ID']."*\r\nID_WP: ".$event->orden['ID_WP']."\r\nCLIENTE: _".$event->orden['NOMBRES']." ".$event->orden['APELLIDOS']."_\r\nCELULAR: ".$event->orden['CELULAR']."\r\nTOTAL: ".$event->orden['TOTAL']."\r\n";

        $message = $twilio->messages
                          ->create("whatsapp:+595973855499", // to
                                   [
                                       "from" => "whatsapp:+14155238886",
                                       "body" => $data
                                   ]
                          );

        print($message->sid);

        /*  --------------------------------------------------------------------------------- */
        
        //"I sent \r\n this message in under 10 minutes!"
        // "🎶I am _not_ ~pushing~ throwing away my *shot*!"
    }
}
