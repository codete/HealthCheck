# Purpose

HealthCheck library aims to provide an easy way to write arbitrary system checks that are ran regularly. For 
library it does not matter what you check, what matters is whether the check result is OK, WARNING or ERROR 
and how the library can be of help to notify you about the results.

# Requirements

Library's only requirement is that you use PHP not older than 7.1. The library also provides few out of the box
integrations, you can check the `require-dev` section. Also for all Symfony lovers we provide a bundle that will
turn library integration into a breeze.

# HealthCheck

Any health check must implement `\Codete\HealthCheck\HealthCheck` interface. To register your check in the library
you can tag service with `hc.health_check` tag if you're using Symfony or add it to 
`\Codete\HealthCheck\HealthCheckRegistry` manually.

To fulfill the `HealthCheck` contract your implementation must provide 3 methods:

* `getName` - human readable name of the check
* `check` - actual logic of your check
* `validUntil` - optional date to indicate until when the check is considered safe. Useful for 3rd party API health 
checks when you know when used version will no longer be supported. In practice checks that are OK but valid date 
is due will turn to warnings.

The `check` method must return `\Codete\HealthCheck\HealthStatus` object which encapsulates check result (OK, 
WARNING or ERROR) and an optional message. Basing on status result Result Handlers can act accordingly.

# ResultHandler

Result handler are services that are handling health check results. Any Result handler must implement
`\Codete\HealthCheck\ResultHandler` interface. To register your handler in the library you can tag service with
`hc.result_handler` and provide unique `id` parameter if you're using Symfony or add it to 
`\Codete\HealthCheck\ResultHandlerRegistry` manually. For example implementations you can take a look into 
`\Codete\HealthCheck\ResultHandler\` namespace.

# Commands

Bundle provides two commands:

* `health-check:run <fqcn>` to run one health check and output its status
* `health-check:run-all` to run all registered health checks and output results in a nice way

# Bundle's configuration reference

```yaml
health_check:
  handlers:
    psr3:
      type: psr3                      # PSR3 compatible handler
      id: logger                      # service identifier
      level: warning                  # level that should be used for reporting
    chain:
      type: chain                     # groups together many handlers
      members: [ psr3, name_of_your_tagged_handler ]
    elephpant:
      type: remembering               # aggregates results to be fetched later
  status:                             # choose which handler should be called for each result status
    green: ~                          # no reporting
    yellow: elephpant                 # use "elephpant" handler
    red: chain                        # use "chain" handler
```
