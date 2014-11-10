<?php
namespace Icecave\Interlude;

interface InvokerInterface
{
    /**
     * Repeat non-blocking operation until it succeeds, a timeout period is
     * reached, or the maximum number of attempts have been performed.
     *
     * The operation function is invoked with two parameters, the remaining
     * timeout period (in seconds) and the remaining number of attempts,
     * including this one.
     *
     * The operation is considered successful if it returns a value that fails a
     * strict comparison with false (ie, $value !== false).
     *
     * @param callable      $operation The non-blocking operation to perform.
     * @param integer|float $timeout   The maximum time to wait for the operation to succeed, in seconds.
     * @param integer       $attempts  The maximum number of attempts to perform.
     * @param integer|float $delay     How long to delay between each attempt, in seconds.
     *
     * @return mixed                      The return value of the operation.
     * @throws AttemptsExhaustedException If the operation is attempted the maximum number of times without success.
     * @throws TimeoutException           If the timeout is reached before the operation is invoked successfully.
     */
    public function invoke(
        $operation,
        $timeout = INF,
        $attempts = INF,
        $delay = 0
    );
}
