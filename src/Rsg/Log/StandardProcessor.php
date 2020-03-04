<?php

namespace Rsg\Log;

/**
 * Standard Monolog Processer for RSG libraries
 *
 * The StandardProcessor can be used by itself or be deocrated by another
 * processor.
 *
 * This will implement the ProcessorInterface defined by Monolog, but does not
 * explicitly do so to avoid the Composer dependency.
 *
 * @final
 */
final class StandardProcessor implements Processor
{
    /**
     * environment
     *
     * @var string
     */
    private $environment;

    /**
     * service
     *
     * @var string
     */
    private $service;

    /**
     * datetime_format
     *
     * @var string
     */
    private $datetime_format;


    /**
     * Create a new log processor
     *
     * @param string $environment
     * @param string $service
     * @param string $datetime_format The format to be used for timestamps,
     *                                defaults to ISO8601 with microseconds
     */
    public function __construct(
        $environment,
        $service,
        $datetime_format = 'Y-m-d\TH:i:s.uO'
    ) {
        $this->environment     = $environment;
        $this->service         = $service;
        $this->datetime_format = $datetime_format;
    }


    /**
     * @param array $record The record to be logged
     * @return array The array of data to be logged
     */
    public function __invoke(array $record)
    {
        $altered_record = [
            'env'     => $this->environment,
            'service' => $this->service,
        ];

        $record = $this->handleSeverity($record);
        $record = $this->handleDatetime($record);

        return $record + $altered_record;
    }


    /**
     * Update the "log_level" to be "severity"
     *
     * @param array $record
     * @return array The updated record
     */
    private function handleSeverity(array $record)
    {
        if (isset($record[ 'level_name' ])) {
            $record[ 'severity' ] = $record[ 'level_name' ];
            unset($record[ 'level_name' ]);
        }

        return $record;
    }


    /**
     * Update the "timestamp"
     *
     * @param array $record
     * @return array The updated record
     */
    private function handleDatetime(array $record)
    {
        if (isset($record[ 'datetime' ])) {
            $record[ 'timestamp' ] = ( $record[ 'datetime' ] instanceof \DateTimeInterface )
                ? $record[ 'datetime' ]->format($this->datetime_format)
                : $record[ 'datetime' ];

            unset($record[ 'datetime' ]);
        }

        return $record;
    }
}
