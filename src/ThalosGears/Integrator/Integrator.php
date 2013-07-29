<?php
namespace ThalosGears\Integrator;

class Integrator
{
    /**
     * Integrator allows to execute commands of an action in a project and save it, or load a execution results
     *
     * @param  string   $id          Unique key for the integrator (the same as the project)
     * @param  string   $name        The name of the integrator 
     * @param  string   $description Project's description 
     * @param  array    $nodes       The name of the integrator
     * @param  string   $action      A group of tasks to execute (post-commit, pre-commit, ...)
     * @param  array    $tasks       Tasks to execute (need an action)   
     * @param  string   $status      Return "done" on success, "error" on failure.
     * @param  DateTime $start       
     * @param  DateTime $end
     
     * @param  int      $revision   
     * @param  string   $file       path which contains the result of action's execution
     *
     */
         
    public $id;
	public $name;
    public $description;
	protected $nodes = array();
	public $tasks = array();
	protected $logger;
	public $status;
	public $start;
	public $end;
	public $action;
    public $revision;
    public $file;
	
	public function __construct($id, $name, $description, $nodes, $tasks) 
    {
		$this->id = $id;
        $this->name = $name;
        $this->description = $description;
		$this->nodes = $nodes;
		$this->tasks = $tasks;
	}
	
    public function __destruct() 
    {
		if ($this->status == 'running') {
            $this->status = 'canceled';
            $this->persist('/tmp/');
        }
	}
    
    public function getId() 
    {
        return $this->id;
    }
        
	public function getDescription() 
    {
		return $this->description;
    }
    public function getStatus() 
    {
		return $this->status;
    }
    
	public function run($action, $path, $revision) 
    {
        $this->revision = $revision;
        $this->action = $action;
		$this->start = new \DateTime();
		$this->status = 'running';
		if ($this->runTasks()) {
			$this->status = 'done';
		} else {
			$this->status = 'error';
		}
		$this->end = new \DateTime();
		$this->persist($path);
	}

	protected function runTasks() 
    {
        // Get all tasks for the action and run
		foreach ($this->tasks[$this->action] as $name => $task) {
			$task->runCommands($task->node);
            if ('error' == $task->status) {
                return false;
            }
		}
		return true;
	}
	
    public function getFileName($path) 
    {
        return $path.'/'.$this->id.'_'.$this->start->format('Ymd-His').'_'.$this->action.'_'.$this->revision.'.dat';
    }
        
	protected function persist($path) 
    {
		$this->file = basename($this->getFileName($path));
        file_put_contents($this->getFileName($path), serialize($this));
	}
    
    public static function load($file) 
    {
        $integrator = unserialize(file_get_contents($file));
        $integrator->file = basename($file);
        return $integrator;
    }
    public function getNodes() 
    {
        return $this->nodes;
    }
}

