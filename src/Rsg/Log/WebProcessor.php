<?php

namespace Rsg\Log;

use \Monolog\Processor\WebProcessor as BaseProcessor;

/**
 * Class: WebProcessor
 *
 * Add logging for web requests. This extends `\Monolog\Processor\WebProcessor`
 * and decorates another `\Rsg\Log\Processor`.
 *
 * @final
 */
final class WebProcessor
    extends BaseProcessor
    implements Processor
{
    /**
     * _processor
     *
     * @var Processor
     */
    private $_processor;


    /**
     * @param Processor $processor The processor that this is decorating
     */
    public function __construct( Processor $processor )
    {
        parent::__construct();
        $this->_processor = $processor;
    }


    /**
     * __invoke
     *
     * @param array $record The record being logged
     * @return array the record to be logged
     */
    public function __invoke( array $record )
    {
        $record = parent::__invoke( $record );
        return $this->_processor->__invoke( $record );
    }
}
