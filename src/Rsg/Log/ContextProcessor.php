<?php

namespace Rsg\Log;

/**
 * A decorator class used to copy data to a more useful place in the record.
 *
 * Decorates a monolog ProcessorInterface. The primary goal is to check
 * the context for one of the keys we want to easily search and bring it to the
 * root of the record.
 *
 * @final
 */
final class ContextProcessor implements Processor
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @var array
     */
    private $keys_to_escalate = [
        'quote_id',
        'contract_id',
        'user_id',
        'agent_code',
        'service',
        'trace_id',
        'span_id',
        'indication_id',
    ];


    /**
     * @param Processor $processor        The processor being decorated
     * @param array     $keys_to_escalate The keys in the context that should be
     *                                      copied to the root record
     */
    public function __construct(Processor $processor, array $keys_to_escalate = [])
    {
        $this->processor = $processor;

        if (!empty($keys_to_escalate)) {
            $this->keys_to_escalate = $keys_to_escalate;
        }
    }


    /**
     * @inheritDoc
     */
    public function __invoke(array $record)
    {
        foreach ($this->keys_to_escalate as $key) {
            $record = $this->escalateKey($record, $key);
        }

        return $this->processor
            ->__invoke($record);
    }


    /**
     * Escalate a key/value
     *
     * Check the context for a key and push the key and its value up to the
     *   top-level of the log.
     *
     * @param array  $record The log record
     * @param string $key    The key to escalate
     * @return array The new log record
     */
    public function escalateKey(array $record, $key)
    {
        if (isset($record['context'][$key])) {
            $record = $this->preserveKey($record, $key);
            $record[$key] = $record['context'][$key];
            unset($record['context'][$key]);
        }

        return $record;
    }


    /**
     * Preserve a key/value
     *
     * Copy a top-level key/value into the record's context. The new key will be
     *   preceded by an underscore.
     *
     * @param array  $record The log record
     * @param string $key    The key to preserve
     * @return array The new log record
     */
    public function preserveKey(array $record, $key)
    {
        if (isset($record[$key])) {
            $record['context']['_' . $key] = $record[$key];
        }

        return $record;
    }
}
