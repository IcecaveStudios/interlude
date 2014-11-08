<?php
namespace Icecave\Interlude;

use Eloquent\Phony\Phpunit\Phony;
use Exception;
use Icecave\Interlude\Exception\InterludeExceptionInterface;
use Icecave\Interlude\Exception\RetriesExhaustedException;
use Icecave\Interlude\Exception\TimeoutException;
use Icecave\Isolator\Isolator;
use PHPUnit_Framework_TestCase;

class InvokerTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->isolator = Phony::mock('Icecave\Isolator\Isolator');
        $this->operation = Phony::stub();
        $this->regularException = new Exception('The operation failed!');
        $this->interludeException = Phony::mock(
            array(
                'Icecave\Interlude\Exception\InterludeExceptionInterface',
                'Exception',
            ),
            array(
                'Test exception!',
            )
        );

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
            ->throws($this->regularException)
            ->returns(123);

        $result = $this->invoker->invoke(
            $this->operation,
            100,
            200
        );

        Phony::inOrder(
            $this->operation->calledWith(100, 200),
            $this->isolator->usleep->calledWith(0),
            $this->operation->calledWith(90, 199)
        );

        $this->assertSame(
            123,
            $result
        );
    }

    public function testInvokeWithOperationThatThrowsInterludeException()
    {
        $this
            ->operation
            ->throws($this->interludeException->mock());

        $this->setExpectedException(
            'Icecave\Interlude\Exception\InterludeExceptionInterface',
            'Test exception!'
        );

        $this->invoker->invoke(
            $this->operation
        );
    }

    public function testInvokeWithOperationThatFailsDueToExhaustedRetryAttempts()
    {
        $this
            ->operation
            ->throws($this->regularException);

        $this->setExpectedException(
            'Icecave\Interlude\Exception\RetriesExhaustedException'
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

    public function testInvokeWithOperationThatFailsDueToTimeout()
    {
        $this
            ->operation
            ->throws($this->regularException);

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
}
