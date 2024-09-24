<?php

declare(strict_types=1);

namespace App\Infrastructure\Monolog;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;

readonly class MaskProcessor implements ProcessorInterface
{
    public function __invoke(LogRecord $record): LogRecord
    {
        if (!isset($record->context['mask'])) {
            return $record;
        }

        try {
            $context = $record->context;
            $mask = $context['mask'];
            unset($context['mask']);

            if (is_array($mask)) {
                $context = $this->mask($context, $mask);
            }

            return $record->with(context: $context);
        } catch (\Throwable) {
            return $record;
        }
    }

    private function mask(iterable $context, array $mask, string $path = ''): iterable
    {
        $path = '' === $path ? $path : $path.'.';

        foreach ($context as $key => &$value) {
            if (is_array($value)/* || is_object($value)*/) {
                $value = $this->mask($value, $mask, $path.$key);
            } elseif (is_scalar($value) && in_array($path.$key, $mask, true)) {
                $value = '<mask>';
            }
        }

        return $context;
    }
}
