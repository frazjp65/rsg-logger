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
final class ContextProcessor
    implements Processor
{
    /**
     * @var ProcessorInterface
     */
    private $_processor;

    /**
     * @var array
     */
    private $_keys_to_escalate = [
        'quote_id',
        'contract_id',
        'user_id',
        'agent_code',
    ];


    /**
     * @param ProcessorInterface $processor        The processor being decorated
     * @param array              $keys_to_escalate The keys in the context that should be
     *                                             copied to the root record
     */
    public function __construct( Processor $processor, array $keys_to_escalate = [] )
    {
        $this->_processor = $processor;

        if ( !empty( $keys_to_escalate ) )
        {
            $this->_keys_to_escalate = $keys_to_escalate;
        }
    }


    /**
     * @{inheritdoc}
     */
    public function __invoke( array $record )
    {
        foreach ( $this->_keys_to_escalate as $key )
        {
            // do not overwrite an existing key
            if ( isset( $record[ 'context' ][ $key ] ) && ! isset( $record[ $key ] ) )
            {
                $record[ $key ] = $record[ 'context' ][ $key ];
            }
        }

        return $this->_processor
            ->__invoke( $record );
    }
}
