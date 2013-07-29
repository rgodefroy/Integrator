<?php
namespace ThalosGears\Integrator;

/*
 * We use ThalosGears\Logger 
 * But Silex require to implement \Symfony\Component\HttpKernel\Log\LoggerInterface
 * Both implements PSR-3-logger-interface
 * So just extend and implement
 */
class Logger extends \ThalosGears\Logger implements \Symfony\Component\HttpKernel\Log\LoggerInterface
{
}
