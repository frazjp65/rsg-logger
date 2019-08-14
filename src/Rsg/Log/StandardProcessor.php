<?php

namespace Rsg\Log;

/**
 * Class: StandardProcessor
 *
 * Monolog Processer for RSG libraries
 *
 * The StandardProcessor can be used by itself or be deocrated by another
 * processor.
 *
 * This will implement the ProcessorInterface defined by Monolog, but does not
 * explicitly do so to avoid the Composer dependency.
 *
 * @final
 */
final class StandardProcessor
    implements Processor
{
    /**
     * _environment
     *
     * @var string
     */
    private $_environment;

    /**
     * _service
     *
     * @var string
     */
    private $_service;


    /**
     * Create a new log processor
     *
     * @param string $environment
     * @param string $service
     */
    public function __construct( $environment, $service )
    {
        $this->_environment = $environment;
        $this->_service     = $service;
    }


    /**
     * @param array $record The record to be logged
     * @return array The array of data to be logged
     */
    public function __invoke( array $record )
    {
        $altered_record = [
            'env'     => $this->_environment,
            'service' => $this->_service,
        ];

        if ( isset( $record[ 'level_name' ] ) )
        {
            $record[ 'severity' ] = $record[ 'level_name' ];
            unset( $record[ 'level_name' ] );
        }

        return $record + $altered_record;
    }
}
