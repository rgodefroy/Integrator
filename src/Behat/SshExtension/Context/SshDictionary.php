<?php

namespace Behat\Ssh\Context;

trait SshDictionary
{
    private $sessionRegistry;

    public function setSessionRegistry(SessionRegistry $sessionRegistry)
    {
        $this->sessionRegistry = $sessionRegistry;
    }

    public function getSessionRegistry()
    {
        return $this->sessionRegistry;
    }

    public function getSshSession($name)
    {
        return $this->sessionRegistry->getSession($name);
    }

    public function sshExec($sessionName, $command)
    {
        // @todo add check for exit code
        return $this->getSshSession($sessionName)->getExec()->run($command);
    }
}