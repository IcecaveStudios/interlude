<?php
namespace Icecave\Interlude;

use Eloquent\Phony\Phpunit\Phony;
use Exception;
use Icecave\Interlude\Exception\AttemptsExhaustedException;
use Icecave\Interlude\Exception\TimeoutException;
use Icecave\Isolator\Isolator;
use PHPUnit_Framework_TestCase;

class InvokerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->isolator = Phony::fullMock('Icecave\Isolator\Isolator');
        $this->operation = Phony::stub();

        $this
            ->isolator
            ->microtime
            ->with(true)
            ->returns(10.0)
            ->returns(20.0)
            ->returns(30.0)
            ->returns(40.0);

        $this->invoker = new Invoker(
            $this->isolator->mock()
        );
    }

    public function testInvokeWithOperationThatSucceeds()
    {
        $this
            ->operation
            ->returns(123);

        $result = $this->invoker->invoke(
            $this->operation,
            100,
            200
        );

        $this
            ->operation
            ->calledWith(100, 200);

        $this->assertSame(
            123,
            $result
        );
    }

    public function testInvokeWithOperationThatFailsThenSucceeds()
    {
        $this
            ->operation
            ->returns(false)
            ->returns(123);

        $result = $this->invoker->invoke(
            $this->operation,
            100,
            200
        );

        Phony::inOrder(
            $this->operation->calledWith(100, 200),
            $this->operation->calledWith(90, 199)
        );

        $this->assertSame(
            123,
            $result
        );
    }

    public function testInvokeWithOperationThatThrows()
    {
        $exception = new Exception('Test exception!');

        $this
            ->operation
            ->throws($exception);

        $this->setExpectedException(
            'Exception',
            'Test exception!'
        );

        $this->invoker->invoke(
            $this->operation
        );
    }

    public function testInvokeWithOperationThatFailsDueToTimeout()
    {
        $this
            ->operation
            ->returns(false);

        $this->setExpectedException(
            'Icecave\Interlude\Exception\TimeoutException'
        );

        try {
            $this->invoker->invoke(
                $this->operation,
                25
            );
        } catch (Exception $e) {
            Phony::inOrder(
                $this->operation->calledWith(25, INF),
                $this->operation->calledWith(15, INF),
                $this->operation->calledWith( 5, INF)
            );

            throw $e;
        }
    }

    public function testInvokeWithZeroTimeout()
    {
        $this
            ->operation
            ->returns(false);

        $this->setExpectedException(
            'Icecave\Interlude\Exception\TimeoutException'
        );

        try {
            $this->invoker->invoke(
                $this->operation,
                25
            );
        } catch (Exception $e) {
            $this->operation->calledWith(25, INF);

            throw $e;
        }
    }

    public function testInvokeWithInvalidTimeout()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Timeout must be zero or greater.'
        );

        $this->invoker->invoke(
            $this->operation,
            -1.0
        );
    }

    public function testInvokeWithOperationThatFailsDueToExhaustedAttempts()
    {
        $this
            ->operation
            ->returns(false);

        $this->setExpectedException(
            'Icecave\Interlude\Exception\AttemptsExhaustedException'
        );

        try {
            $this->invoker->invoke(
                $this->operation,
                INF,
                3
            );
        } catch (Exception $e) {
            Phony::inOrder(
                $this->operation->calledWith(INF, 3),
                $this->operation->calledWith(INF, 2),
                $this->operation->calledWith(INF, 1)
            );

            throw $e;
        }
    }

    public function testInvokeWithInvalidAttempts()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Attempts must be one or greater.'
        );

        $this->invoker->invoke(
            $this->operation,
            INF,
            0
        );
    }

    public function testInvokeWithNonZeroDelay()
    {
        $this
            ->operation
            ->returns(false)
            ->returns(false)
            ->returns(123);

        $result = $this->invoker->invoke(
            $this->operation,
            INF,
            INF,
            0.5
        );

        Phony::inOrder(
            $this->operation->calledWith(INF, INF),
            $this->isolator->usleep->calledWith(500000),
            $this->operation->calledWith(INF, INF),
            $this->isolator->usleep->calledWith(500000),
            $this->operation->calledWith(INF, INF)
        );

        $this->assertSame(
            123,
            $result
        );
    }

    public function testInvokeWithInvalidDelay()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Delay must be zero or greater.'
        );

        $this->invoker->invoke(
            $this->operation,
            INF,
            INF,
            -0.1
        );
    }

}
