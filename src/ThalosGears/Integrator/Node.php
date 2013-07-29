<?php
namespace ThalosGears\Integrator;

use Ssh;
use RuntimeException;

class Node 
{

    /**
     * Node is used to connect to remote or local host by SSH 
     *
     * @param  string                       $name           The name of the node 
     * @param  string                       $host           Host
     * @param  integer                      $port           Port
     * @param  string                       $user           Ssh user
     * @param  string                       $password       Ssh password
     * @param  Session                      $session    
     * @param  Exec                         $exec           Ressource for execution in a session
     * @param  Ssh\Configuration            $configuration
     * @param  Ssh\Authentication\Password  $authentication     
     * 
     */
     
	public $name;
    public $host;
    public $port;
    public $user;
    public $password;
    public $session;
    public $exec;
    protected $configuration;
    protected $authentication;

    public function __construct($name, $host, $port, $user, $password) 
    {
        $this->name = $name;
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->configuration = new Ssh\Configuration($this->host, $this->port);
        $this->authentication = new Ssh\Authentication\Password($this->user, $this->password);
    }
    
    protected function getSession() 
    {
        
        if (!$this->session) {
            $this->session = new Ssh\Session($this->configuration, $this->authentication);
        }
        return $this->session;
    }
    
    protected function getExec() 
    {
        if (!$this->exec) {
            $this->exec = $this->getSession()->getExec();
        }
        return $this->exec;
    }
    
    public function exec($command)
    {
        $exec = $this->getExec();
        $commandWithExit = $command.';echo -en "\n$?"';
        try {
            $result = $exec->run($commandWithExit);
        } catch (RuntimeException $e) {
            throw $e;
        }
        if (! preg_match( "/^(.*)\n(0|-?[1-9][0-9]*)$/s", $result, $matches)) {
            throw new RuntimeException("output didn't contain return status\n$commandWithExit\n$result");
        }
        if ($matches[2] !== "0") {
            throw new RuntimeException($matches[1]);
        }
        return $matches[1];
    }
}
