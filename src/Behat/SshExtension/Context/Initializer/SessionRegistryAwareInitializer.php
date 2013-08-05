<?php

namespace Behat\SshExtension\Context\Initializer;

use Behat\Behat\Context\Initializer\InitializerInterface;
use Behat\Behat\Context\ContextInterface;
use Behat\SshExtension\SessionRegistry;
use Behat\SshExtension\SessionRegistryAwareInterface;

class SessionRegistryAwareInitializer implements InitializerInterface
{
    private $sessionRegistry;

    public function __construct(SessionRegistry $sessionRegistry)
    {
        $this->sessionRegistry = $sessionRegistry;
    }

    public function supports(ContextInterface $context)
    {
        if ($context instanceof SessionRegistryAwareInterface) {
            return true;
        }

        $refl = new \ReflectionObject($context);
        if (method_exists($refl, 'getTraitNames')) {
            if (in_array('Behat\\SshExtension\\Context\\SshDictionary', $refl->getTraitNames())) {
                return true;
            }
        }

        return false;
    }

    public function initialize(ContextInterface $context)
    {
        $context->setSessionRegistry($this->sessionRegistry);
    }
}
