<?php
namespace ThalosGears\Integrator;

use RuntimeException;

class Task
{
    /**
     * Task
     *
     * @param  string   $name       The name of the task 
     * @param  string   $node       
     * @param  array    $commands   
     * @param  string   $status
     * @param  string   $output            
     * 
     */    
	public $name;
    public $node;
	public $commands;
    public $status = 'pending';
    public $output;
    
    public function __construct($name, $node, $commands = array()) 
    {
        $this->name = $name;
        $this->node = $node;
        $this->commands = array();
        $this->addCommands($commands);
    }
    
    public function runCommands($node) 
    {
        foreach ($this->commands as $command) {
            $exitCode = 0;
            $output = array();
            try {
                $command->output = $node->exec($command->command);
            } catch (RuntimeException $e) {
                $command->output = $e->getMessage();
                $command->status = 'error';
                $this->status = 'error';
                return false;
            }
            $command->status = 'done';
        }
        $this->status = 'done';
    }
    
    public function addCommands($commands) 
    {
        foreach ($commands as $command) {
            $this->addCommand($command);
        }
    }
    
    public function addCommand($command) 
    {
        $command = new Command($command);
        $this->commands[] = $command;
    }
        
}
