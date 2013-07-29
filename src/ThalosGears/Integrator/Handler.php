<?php
namespace ThalosGears\Integrator;

class Handler
{
    public $signal;
    
	public function __construct($signal) 
    {
		$this->signal = $signal;
	}    
    
    private function sig_handler()
    {

        switch ($this->signal) {
            case SIGTERM:
                echo "Exit signal";
                exit;
                break;
            //case SIGHUP:
                // handle restart tasks
                //  break;
            //case SIGUSR1:
                //  echo "Caught SIGUSR1...\n";
                // break;
            //default:
                // handle all other signals
        }
        // setup signal handlers
        pcntl_signal(SIGTERM, "sig_handler");
        //pcntl_signal(SIGHUP,  "sig_handler");
        //pcntl_signal(SIGUSR1, "sig_handler");

        }



}


