<?php
namespace Icecave\Interlude\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class TimeoutExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new TimeoutException($previous);

        $this->assertSame(
            'The operation timed out.',
            $exception->getMessage()
        );

        $this->assertSame(
            $previous,
            $exception->getPrevious()
        );
    }
}
