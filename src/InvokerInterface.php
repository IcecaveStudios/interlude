<?php
namespace Icecave\Interlude;

interface InvokerInterface
{
    /**
     * Repeat an operation until it succeeds, times-out or too many attempts are
     * performed.
     *
     * The operation function is invoked with two parameters, the remaining
     * timeout period (in seconds) and the number of retries remaining.
     *
     * The operation is considered successful if it returns without throwing an
     * exception, in which case execute() returns the operation's return value.
     *
     * If the operation directly throws an exception that implements
     * InterludeExceptionInterface the exception will propagate immediately,
     * bypassing any remaining retries.
     *
     * @param callable      $operation The non-blocking operation to perform.
     * @param integer|float $timeout   The maximum time to wait for the operation to succeed, in seconds.
     * @param integer       $retries   The maximum number of retries to perform.
     * @param integer|float $delay     How long to delay between each attempt, in seconds.
     *
     * @return mixed               The return value of the operation if successful.
     * @throws RetriesExhaustedException If the operation is retried the maximum number of times without success.
     * @throws TimeoutException    If the timeout is reached before the operation is invoked successfully.
     */
    public function invoke(
        callable $operation,
        $timeout = INF,
        $retries = INF,
        $delay = 0
    );
}
