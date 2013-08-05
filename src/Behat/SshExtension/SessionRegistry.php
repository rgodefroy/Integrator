<?php

namespace Behat\SshExtension;

class SessionRegistry
{
    private $sessions;

    public function setSession($name, \Ssh\Session $session)
    {
        $this->sessions[$name] = $session;
    }

    public function getSession($name)
    {
        if (false === $this->hasSession($name)) {
            throw new \Exception(sprintf(
                'Session "%s" is not defined.',
                $name
            ));
        }

        return $this->sessions[$name];
    }

    public function hasSession($name)
    {
        return isset($this->sessions[$name]);
    }
}