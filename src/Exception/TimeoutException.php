<?php
namespace Icecave\Interlude\Exception;

use Exception;
use RuntimeException;

class TimeoutException extends RuntimeException implements InterludeExceptionInterface
{
    public function __construct(Exception $previous = null)
    {
        parent::__construct(
            'The operation timed out.',
            0,
            $previous
        );
    }
}
