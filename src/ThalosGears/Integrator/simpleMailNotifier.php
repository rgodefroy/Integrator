<?php
namespace ThalosGears\Integrator;

class simpleMailNotifier implements NotifierInterface
{
	public $recipients;
	
	public function __construct($recipients) 
    {
		$this->recipients = implode(",", $recipients);
    }

	public function notify(Integrator $integrator) 
	{
	if (!mail($this->recipients, $integrator->getId(), $integrator->getDescription())) { 
		throw new \Exception ("Can't send email notification");
		}
	}
}
