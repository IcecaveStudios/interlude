<?php
namespace Icecave\Interlude\Exception;

use Exception;
use RuntimeException;

class RetriesExhaustedException extends RuntimeException implements InterludeExceptionInterface
{
    public function __construct(Exception $previous = null)
    {
        parent::__construct(
            'The operation has been retried the maximum number of times.',
            0,
            $previous
        );
    }
}
