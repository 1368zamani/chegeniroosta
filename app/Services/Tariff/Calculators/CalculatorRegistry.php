<?php

namespace App\Services\Tariff\Calculators;

use InvalidArgumentException;

class CalculatorRegistry
{
    /** @var array<string, class-string<TariffCalculator>> */
    private array $map = [];

    public function register(string $key, string $calculatorClass): self
    {
        if (!class_exists($calculatorClass)) {
            throw new InvalidArgumentException("Calculator class not found: {$calculatorClass}");
        }
        if (!in_array(TariffCalculator::class, class_implements($calculatorClass), true)) {
            throw new InvalidArgumentException("{$calculatorClass} must implement TariffCalculator");
        }
        $this->map[$key] = $calculatorClass;
        return $this;
    }

    public function has(string $key): bool
    {
        return isset($this->map[$key]);
    }

    public function get(string $key): TariffCalculator
    {
        if (!$this->has($key)) {
            throw new InvalidArgumentException("Calculator not registered for key: {$key}");
        }
        return app($this->map[$key]);
    }

    public function all(): array
    {
        return array_keys($this->map);
    }
}
