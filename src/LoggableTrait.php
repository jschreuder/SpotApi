<?php declare(strict_types = 1);

namespace Spot\Api;

use Psr\Log\LoggerInterface;

trait LoggableTrait
{
    /** @var  LoggerInterface */
    private $logger;

    protected function log(string $level, string $message, array $metadata = [])
    {
        $this->logger->log($level, '[' . get_class($this) . '] ' . $message, $metadata);
    }
}
