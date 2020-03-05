<?php

namespace Rsg\Log;

use Monolog\Processor\WebProcessor as BaseProcessor;

/**
 * Processor to add logging for web requests.
 *
 * This extends `\Monolog\Processor\WebProcessor`
 * and decorates another `\Rsg\Log\Processor`.
 *
 * @final
 */
final class WebProcessor extends BaseProcessor implements Processor
{
    /**
     * processor
     *
     * @var Processor
     */
    private $processor;


    /**
     * @param Processor $processor The processor that this is decorating
     */
    public function __construct(Processor $processor)
    {
        parent::__construct();
        $this->processor = $processor;
    }


    /**
     * __invoke
     *
     * @param array $record The record being logged
     * @return array the record to be logged
     */
    public function __invoke(array $record): array
    {
        $record = parent::__invoke($record);
        return $this->processor->__invoke($record);
    }
}
