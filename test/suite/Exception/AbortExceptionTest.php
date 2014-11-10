<?php
namespace Icecave\Interlude\Exception;

use Exception;
use PHPUnit_Framework_TestCase;

class AbortExceptionTest extends PHPUnit_Framework_TestCase
{
    public function testException()
    {
        $previous = new Exception;
        $exception = new AbortException($previous);

        $this->assertSame(
            'The operation has been aborted.',
            $exception->getMessage()
        );

        $this->assertSame(
            $previous,
            $exception->getPrevious()
        );
    }
}
