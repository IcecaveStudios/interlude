<?php
namespace Icecave\Interlude;

use Exception;
use Icecave\Isolator\Isolator;
use Icecave\Interlude\Exception\InterludeExceptionInterface;
use Icecave\Interlude\Exception\RetriesExhaustedException;
use Icecave\Interlude\Exception\TimeoutException;

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
     * Repeat an operation until it succeeds, times-out or too many attempts are
     * performed.
     *
     * The operation function is invoked with two parameters, the remaining
     * timeout period (in seconds) and the number of retries remaining.
     *
     * The operation is considered successful if it returns a value that fails a
     * strict comparison with false (ie, $value !== false) and does not throw an
     * exception.
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
     * @return mixed                     The return value of the operation.
     * @throws RetriesExhaustedException If the operation is retried the maximum number of times without success.
     * @throws TimeoutException          If the timeout is reached before the operation is invoked successfully.
     */
    public function invoke(
        $operation,
        $timeout = INF,
        $retries = INF,
        $delay = 0
    ) {
        $start     = $this->isolator->microtime(true);
        $remaining = $timeout;
        $delay    *= 1000000; // convert seconds to micros for usleep()

        retry:

        try {
            $e = null;
            $result = $operation($remaining, $retries);

            if (false !== $result) {
                return $result;
            }
        } catch (InterludeExceptionInterface $e) {
            throw $e;
        } catch (Exception $e) {
            // continue ...
        }

        if (--$retries <= 0) {
            throw new RetriesExhaustedException($e);
        }

        $remaining = $timeout - ($this->isolator->microtime() - $start);

        if ($remaining <= 0) {
            throw new TimeoutException($e);
        }

        $this->isolator->usleep($delay);
        goto retry;
    }

    private $isolator;
}
