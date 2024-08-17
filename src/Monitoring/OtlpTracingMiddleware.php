<?php

namespace App\Monitoring;

use OpenTelemetry\API\Globals;
use OpenTelemetry\API\Trace\Propagation\TraceContextPropagator;
use OpenTelemetry\API\Trace\SpanKind;
use OpenTelemetry\Context\ContextInterface;
use OpenTelemetry\Context\Propagation\ArrayAccessGetterSetter;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpReceivedStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class OtlpTracingMiddleware implements MiddlewareInterface
{

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $handle = fn() => $stack->next()->handle($envelope, $stack);

        $stamp = $envelope->last(AmqpReceivedStamp::class);
        if ($stamp) {
            $context = $this->extractContext($stamp);
            $handle = $this->withinTraceSpan($handle, $context, $envelope->getMessage()::class);
        }

        return $handle();
    }

    private function withinTraceSpan(callable $handle, ContextInterface $context, $messageName): callable {
        return function() use ($handle, $context, $messageName) {
            $tracer = Globals::tracerProvider()->getTracer('my.org.consumer');

            // Start a new span that assumes the context that was injected by the producer
            $span = $tracer
                ->spanBuilder('CONSUME ' . $messageName)
                ->setSpanKind(SpanKind::KIND_CONSUMER)
                ->setParent($context)
                ->startSpan();

            $envelope = $handle();
            $span->end();

            return $envelope;
        };
    }

    private function extractContext(AmqpReceivedStamp $stamp): ContextInterface
    {
        $propagator = TraceContextPropagator::getInstance();
        return $propagator->extract($stamp->getAmqpEnvelope()->getHeaders(), ArrayAccessGetterSetter::getInstance());
    }
}