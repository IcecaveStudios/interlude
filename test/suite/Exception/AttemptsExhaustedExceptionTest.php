<?php
namespace Icecave\Interlude\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class AttemptsExhaustedExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new AttemptsExhaustedException($previous);

        $this->assertSame(
            'The operation has been attempted the maximum number of times.',
            $exception->getMessage()
        );

        $this->assertSame(
            $previous,
            $exception->getPrevious()
        );
    }
}
