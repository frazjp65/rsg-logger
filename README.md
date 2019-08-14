# Description

A collection of logging utilities that can be used by RSG.

# Usage

To use this, one needs to set the config.yml:

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

and the services.yml:

```yaml
Rsg\Log\StandardProcessor:
    tags: [ 'monolog.processor' ]
    arguments: [ '%env%', 'some-service' ]

monolog.formatter:
    class: Monolog\Formatter\JsonFormatter
```

If you decide to use a decorator, make sure the StandardProcessor is the last
one to be processed. Using the WebProcessor as an example, the services.yml
would look like this:

```yaml
Rsg\Log\StandardProcessor:
    arguments: [ '%env%', 'some-service' ]

Rsg\Log\WebProcessor:
    tags: [ 'monolog.processor' ]
    arguments: [ '@Rsg\Log\StandardProcessor' ]

monolog.formatter:
    class: Monolog\Formatter\JsonFormatter
```

