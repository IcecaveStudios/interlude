<?php
namespace Icecave\Interlude;

use Exception;
use Icecave\Interlude\Exception\AttemptsExhaustedException;
use Icecave\Interlude\Exception\TimeoutException;
use Icecave\Isolator\Isolator;
use InvalidArgumentException;

class Invoker implements InvokerInterface
{
    /**
     * @param Isolator|null $isolator
     */
    public function __construct(Isolator $isolator = null)
    {
        $this->isolator = Isolator::get($isolator);
    }

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
    ) {
        if ($timeout < 0) {
            throw new InvalidArgumentException('Timeout must be zero or greater.');
        } elseif ($attempts < 1) {
            throw new InvalidArgumentException('Attempts must be one or greater.');
        } elseif ($delay < 0) {
            throw new InvalidArgumentException('Delay must be zero or greater.');
        }

        $start     = $this->isolator->microtime(true);
        $remaining = $timeout;
        $delay    *= 1000000; // convert seconds to micros for usleep()

        start:

        // Make an attempt ...
        $result = $operation($remaining, $attempts);

        // The attempt was successful ...
        if (false !== $result) {
            return $result;

        // The maximum number of attempts has been exhausted ...
        } elseif (0 === --$attempts) {
            throw new AttemptsExhaustedException();
        }

        // Calculate the timeout remaining ...
        $remaining = $timeout - ($this->isolator->microtime(true) - $start);

        // The timeout period has been reached ...
        if ($remaining <= 0) {
            throw new TimeoutException();
        }

        // Delay before making another attempt ...
        $this->isolator->usleep($delay);

        goto start;
    }

    private $isolator;
}
