<?php

namespace GraphAware\Bolt\Exception;

use GraphAware\Bolt\Protocol\Message\FailureMessage;

class MessageFailureException extends \RuntimeException implements BoltExceptionInterface
{
}