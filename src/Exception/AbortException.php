<?php
namespace Icecave\Interlude\Exception;

use Exception;
use RuntimeException;

class AbortException extends RuntimeException implements InterludeExceptionInterface
{
    public function __construct(Exception $previous = null)
    {
        parent::__construct(
            'The operation has been aborted.',
            0,
            $previous
        );
    }
}
