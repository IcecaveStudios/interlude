<?php
namespace Icecave\Interlude\Exception;

use Exception;
use RuntimeException;

class AttemptsExhaustedException extends RuntimeException implements InterludeExceptionInterface
{
    public function __construct(Exception $previous = null)
    {
        parent::__construct(
            'The operation has been attempted the maximum number of times.',
            0,
            $previous
        );
    }
}
