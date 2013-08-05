<?php

namespace Behat\SshExtension;

interface SessionRegistryAwareInterface
{
    public function setSessionRegistry(SessionRegistry $sessionRegistry);
}