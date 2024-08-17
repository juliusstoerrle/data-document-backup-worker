# Data Document Backup Worker

Worker for async processing of CreateDocument messages with the Data-Document-Backup library.

## Why use this worker?

The worker provides you with a small optimized worker application to consume CreateDocument messages from your own application.
Contrary to including the library in your own application this does not inflate your application and deployment with dependencies.
In addition, the worker can be scaled independently of your application or even run only at specific times of the day to optimize your operations.

The worker is fully equipped with OpenTelemetry instrumentation.

## How to use the worker?

**For testing** a docker image is provided. We recommended to run the image through `docker compose` for ease of configuration.
Please copy TODO LINK this docker compose file to a new folder on your device and start it with 'docker compose -f ddb-test.yml up -d'.

To test, run....

You may copy your custom templates into the ./template folder of the working directory.

**For production** it is recommended to to decide on a template and rendering strategy as well as one messenger transport, depending on the strategy you need different external libraries installed. Information on the available options is included in the README.md of the library.


### Configuration of Logging & OpenTelemetry

````
OTEL_SERVICE_NAME=data-document-backup-worker
OTEL_PHP_AUTOLOAD_ENABLED=true
OTEL_TRACES_EXPORTER=console
OTEL_METRICS_EXPORTER=none
OTEL_LOGS_EXPORTER=console
OTEL_EXPORTER_OTLP_PROTOCOL=grpc
OTEL_EXPORTER_OTLP_ENDPOINT=http://collector:4317
OTEL_PROPAGATORS=baggage,tracecontext
OTEL_PHP_DISABLED_INSTRUMENTATIONS=symfony,io
````

## Implementation Details

The code in this package is a Symfony CLI application build on the Symfony Messenger component.
It mostly provides the configuration required to register the handler from the library and transport(s).

WIP is to remove the symfony framework bundle to reduce dependencies
similar to: https://julien-bouffard.medium.com/build-a-cli-app-with-symfony-components-3777d37de5bd

## How to contribute to the project?

Contributions of any kind are welcome. As a best practice, please create an issue for discussion before a pull request.

### For development:

After cloning the repository you must run `composer install`. You may execute this through the docker-compose environment (see below).

You may use the provided docker-compose.yml in the package root folder to run the application. The local source code will be mounted into the container




