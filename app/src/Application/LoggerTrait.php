<?php

namespace App\Application;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

trait LoggerTrait
{
    public function log($message, $level = LogLevel::DEBUG, array $context = array())
    {
        /** @var $logger LoggerInterface */
        $logger = $this['logger'];

        return $logger->log($level, $message, $context);
    }

    public function logDump($object, $level = LogLevel::DEBUG, array $context = array())
    {
        return $this->log(var_export($object, true), $level, $context);
    }
} 