<?php
namespace Icecave\Interlude\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class RetriesExhaustedExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new RetriesExhaustedException($previous);

        $this->assertSame(
            'The operation has been retried the maximum number of times.',
            $exception->getMessage()
        );

        $this->assertSame(
            $previous,
            $exception->getPrevious()
        );
    }
}
