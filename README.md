# Description

A collection of logging utilities that can be used by RSG.

## Processors

### StandardProcessor

This is the root processor. It does not decorate any other ProcessorInterfaces
like the other ones in this library. It is used to add values that all records
should contain.

#### Usage

```yaml
Rsg\Log\StandardProcessor:
    tags: [ 'monolog.processor' ]
    arguments: [ '%env%', 'some-service' ]
```

### WebProcessor

An adapter for Monolog's WebProcessor. It is used to add "extra" data for web
requests. This also decorates any ProcessorInterface.

#### Usage

```yaml
Rsg\Log\StandardProcessor:
    arguments: [ '%env%', 'some-service' ]

Rsg\Log\WebProcessor:
    tags: [ 'monolog.processor' ]
    arguments: [ '@Rsg\Log\StandardProcessor' ]
```

### ContextProcessor

Copies important data from the context to the root of the record. This also
decorates any ProcessorInterface. The purpose is to allow us to do something
like `$logger->info( 'some message', [ 'important_field' => 1 ] );` and be able
to search "important_field=1" in our log aggregator.

#### Usage

```yaml
Rsg\Log\StandardProcessor:
    arguments: [ '%env%', 'some-service' ]

Rsg\Log\ContextProcessor:
    tags: [ 'monolog.processor' ]
    arguments: [ '@Rsg\Log\StandardProcessor' ]
```

Additionally, one can customize the keys that will be copied.

```yaml
Rsg\Log\StandardProcessor:
    arguments: [ '%env%', 'some-service' ]

Rsg\Log\ContextProcessor:
    tags: [ 'monolog.processor' ]
    arguments:
        $processor: '@Rsg\Log\StandardProcessor'
        $keys_to_escalate:
            - foo
            - bar
            - baz

```

## Recommended Settings

The settings recommended for all RSG apps should include:

```yaml
monolog:
    handlers:
        main:
            type: fingers_crossed
            action_level: error
            passthru_level: info
            handler: nested
            formatter: monolog.formatter
```

and

```yaml
monolog.formatter:
    class: 'Monolog\Formatter\JsonFormatter'
```

