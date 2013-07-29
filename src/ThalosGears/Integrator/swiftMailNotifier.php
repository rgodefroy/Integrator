<?php
namespace ThalosGears\Integrator;

class swiftMailNotifier implements NotifierInterface
{
	public $recipients;
	
	public function __construct($recipients, $app) 
    {
		$this->recipients = $recipients;
		$this->app = $app;
    }


	public function notify(Integrator $integrator) 
	{
		// Create the Transport
		$transport = \Swift_SmtpTransport::newInstance("v-antispam.local.thalos.fr", 26);
		
		// Create the notification
		$notification = \Swift_Message::newInstance();
		$notification->setTo($this->recipients);
		$notification->setSubject('Integrator: '.$integrator->getId().'_'.$integrator->action.' Status: '.$integrator->getStatus());
		$body = ($integrator->getStatus() == 'error') ? ":-( Sorry...\n\n" : ":-) Congratulation...\n\n";
		$body .= vsprintf("project: %s\naction: %s\nrevision: %s\nresult: %s\n", array($integrator->getId(), $integrator->action, $integrator->revision, $integrator->getStatus()));
		$body .= "\n".$this->app['server'].'/load/'.$integrator->file;
		$notification->setBody($body);
		$notification->setFrom("integrator@thalos.fr", "Thalos Integrator");

		// Send the notification
		$mailer = \Swift_Mailer::newInstance($transport);
		$mailer->send($notification);
	}
}


