<?php

namespace Rsg\Log;

/**
 * Interface for RSG log Processors.
 *
 * This is a duplicate of `Monolog\Processor;\ProcessorInterface`, but we do not
 * explicitly use that because we want to only have the dependency for v1.0.0.
 */
interface Processor
{
    /**
     * @return array The processed records
     **/
    public function __invoke( array $records );
}
