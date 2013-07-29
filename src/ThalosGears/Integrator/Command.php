<?php
namespace ThalosGears\Integrator;

class Command
{
    /**
     * Command 
     *
     * @param  string   $command    Command exectued in a Node
     * @param  string   $status     Status of the command (pending by default, done or error)
     * @param  string   $ouput      Ouput of the exectued command
     */
     
	public $command;
    public $status = 'pending';
    public $output;
    
    public function __construct($command) 
    {
        $this->command = $command;
    }
}
